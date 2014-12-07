<?php

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

    protected $agent;
    protected $environmentId;
    protected $isDebug;

    protected function configure()
    {
        parent::configure();
        $this
           ->setName("twigfiddle:run")
           ->setDescription("Executes a twigfiddle (from already prepared environment)")
           ->addOption('debug', 'd', InputOption::VALUE_NONE,
              "If the debug option is set, you'll run an environment located in the debug directory.")
           ->addArgument('environment-id', InputArgument::REQUIRED,
              "Environment where the twigfiddle is stored and will be executed")
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
            $this->agent->addError(Error::E_UNEXPECTED, array ('Exception' => $ex));
        }
        if (!$this->isDebug)
        {
            $this->container->get('debug_manager')->backupIfDebugRequired($this->agent);
        }
        $this->logger->info("Ended execution.");
        $output->write('');
    }

    protected function initErrorHandler()
    {
        register_shutdown_function(function()
        {
            if ((!is_null($err = error_get_last())) && (!in_array($err['type'], array (E_NOTICE, E_WARNING))))
            {
                $this->agent->addError(Error::E_UNEXPECTED, array ('Error' => $err));
                $this->container->get('debug_manager')->backupIfDebugRequired($this->agent);
            }
        });
        return $this;
    }

    protected function initArguments(InputInterface $input)
    {
        $this->environmentId = $input->getArgument('environment-id');
        $this->isDebug = $input->getOption('debug');
        return $this;
    }

    public function initAgent()
    {
        $this->agent = $this->container->get('fiddle_agent');
        $this->agent
           ->setEnvironmentId($this->environmentId)
           ->setIsDebug($this->isDebug)
        ;
        return $this;
    }

    protected function initProcessor()
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

    protected function process()
    {
        $this->container->get('environment_manager')->prepareEnvironment($this->agent);
        $this->container->get('shared_memory_manager')->recoverFiddle($this->agent);
        $this->container->get('engine_manager')->loadTwigEngine($this->agent);
        $this->container->get('context_manager')->extractContext($this->agent);
        $this->container->get('template_manager')->prepareTemplates($this->agent);
        $this->container->get('execute_manager')->executeFiddle($this->agent);

        $this->container->get('compiled_manager')->extractCompiledFiles($this->agent);
        // save to shared memory
    }

}
