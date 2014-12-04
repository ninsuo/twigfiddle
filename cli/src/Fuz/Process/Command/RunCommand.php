<?php

namespace Fuz\Process\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fuz\Framework\Base\BaseCommand;
use Fuz\Process\Entity\Error;
use Fuz\Process\Entity\Context;
use Fuz\Process\Exception\StopExecutionException;

class RunCommand extends BaseCommand
{

    protected $context;
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
        $this->context = new Context($this->environmentId);
        $this->logger->info("Started execution.");
        try
        {
            $this->initArguments($input);
            $this->initProcessor();
        }
        catch (StopExecutionException $ex)
        {
            $this->logger->info("Execution interrupted (see previous errors).");
        }
        catch (\Exception $ex)
        {
            $this->logger->error("An unexpected error occured.", array ('Exception' => $ex));
        }
        if (!$this->isDebug)
        {
            $this->container->get('debug')->backupIfDebugRequired($this->context);
        }
        $this->logger->info("Ended execution.");
        $output->write('');
    }

    protected function initArguments(InputInterface $input)
    {
        $configuration = $this->container->getParameter('environment');

        $this->environmentId = $input->getArgument('environment-id');
        if (!preg_match("/{$configuration['validation']}/", $this->environmentId))
        {
            $this->logger->warning("Invalid environment id given: {$this->environmentId}.");
            $this->context->addError($this->container->get('error_manager')->getError(Error::E_INVALID_ENVIRONMENT_ID));
            throw new StopExecutionException();
        }
        $this->logger->info("Environment ID = {$this->environmentId}");

        $this->isDebug = $input->getOption('debug');
        $this->logger->debug(sprintf("Debug mode = %s", $this->isDebug ? 'enabled' : 'disabled'));
    }

    protected function initProcessor()
    {
        $envId = $this->environmentId;
        $this->container->pushProcessor(function($record) use ($envId)
        {
            $record['extra']['environment_id'] = $envId;
            return $record;
        });
    }

}
