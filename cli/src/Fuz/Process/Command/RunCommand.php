<?php

namespace Fuz\Process\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fuz\Framework\Base\BaseCommand;
use Fuz\Process\Entity\Runner;

class RunCommand extends BaseCommand
{

    protected $runner;
    protected $envId;
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
        $this->envId = $input->getArgument('environment-id');
        $this->isDebug = $input->getOption('debug');
        try
        {
            $this->initProcessor();
            $this->logger->info("Started execution.");
            $this->runner = new Runner();



            if (!$this->isDebug)
            {
                $this->container->get('debug')->backupIfDebugRequired($this->runner);
            }
            $this->logger->info("Ended execution.");
        }
        catch (\Exception $ex)
        {
            $this->logger->error("An unexpected error occured.", array ('Exception' => $ex));
        }

        $output->write('');
    }

    protected function initProcessor()
    {
        $envId = $this->envId;
        $this->container->pushProcessor(function($record) use ($envId)
        {
            $record['extra']['env_id'] = $envId;
            return $record;
        });
    }

}
