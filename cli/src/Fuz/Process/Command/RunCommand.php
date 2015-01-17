<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fuz\Framework\Base\BaseCommand;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;

class RunCommand extends BaseCommand
{

    protected $environmentId;
    protected $isDebug;
    protected $agent;
    protected $memory;

    protected function configure()
    {
        parent::configure();
        $this
           ->setName("twigfiddle:run")
           ->setDescription("Executes a twigfiddle (from already prepared environment)")
           ->addArgument('environment-id', InputArgument::REQUIRED,
              "Environment where the twigfiddle is stored and will be executed")
           ->addOption('debug', 'd', InputOption::VALUE_NONE,
              "If the debug option is set, you'll run an environment located in the debug directory.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info("Started execution.", $input->getArguments());
        try
        {
            $this
               ->initErrorHandler()
               ->initArguments($input)
               ->initAgent()
               ->initProcessor()
               ->process()
            ;
        }
        catch (StopExecutionException $ex)
        {
            $this->logger->debug("Execution interrupted (see previous errors).");
        }
        catch (\Exception $ex)
        {
            $this->agent->addError(Error::E_UNEXPECTED, $ex);
        }
        $this->finish();
        $this->logger->info("Ended execution.");
        $output->write('');
    }

    public function initErrorHandler()
    {
        // This storage is freed on error (case of allowed memory exhausted)
        $this->memory = str_repeat('*', 1024 * 1024);
        register_shutdown_function(function()
        {
            $this->memory = null;
            if ((!is_null($err = error_get_last())) && (!in_array($err['type'], array (E_NOTICE, E_WARNING))))
            {
                // No need to debug main.twig if it contains {{ include('main.twig') }}
                if (!strpos($err['message'], 'Allowed memory size')
                   && !strpos($err['message'], 'Maximum function nesting level'))
                {
                    $this->agent->addError(Error::E_UNEXPECTED, $err);
                    $this->finish();
                }
            }
        });
        return $this;
    }

    public function initArguments(InputInterface $input)
    {
        $this->environmentId = $input->getArgument('environment-id');
        $this->isDebug = $input->getOption('debug');
        return $this;
    }

    public function initAgent()
    {
        $this->agent = $this->get('fiddle_agent');
        $this->agent
           ->setEnvironmentId($this->environmentId)
           ->setIsDebug($this->isDebug)
        ;
        return $this;
    }

    public function initProcessor()
    {
        $envId = $this->environmentId;
        $isDebug = $this->isDebug;
        $this->container->pushProcessor(function($record) use ($envId, $isDebug)
        {
            $record['extra'] = array_merge($record['extra'],
               array (
                    'environment_id' => $envId,
                    'debug' => $isDebug,
            ));
            return $record;
        });
        return $this;
    }

    public function process()
    {
        $this->get('environment_manager')->prepareEnvironment($this->agent);
        $this->get('shared_memory_manager')->recoverFiddle($this->agent);
        $this->get('engine_manager')->loadTwigEngine($this->agent);
        $this->get('context_manager')->extractContext($this->agent);
        $this->get('template_manager')->prepareTemplates($this->agent);
        $this->get('execute_manager')->executeFiddle($this->agent);
        $this->get('compiled_manager')->extractCompiledFiles($this->agent);
        return $this;
    }

    public function finish()
    {
        $this->get('shared_memory_manager')->saveResults($this->agent);
        $this->get('debug_manager')->backupIfDebugRequired($this->agent);
        return $this;
    }

}
