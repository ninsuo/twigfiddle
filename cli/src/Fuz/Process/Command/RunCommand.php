<?php

namespace Fuz\Process\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fuz\Framework\Base\BaseCommand;

class RunCommand extends BaseCommand
{

    protected function configure()
    {
        parent::configure();
        $this
           ->setName("twigfiddle:run")
           ->setDescription("Executes a twigfiddle (from already prepared environment)")
           ->addArgument('environment-id', InputArgument::REQUIRED,
              "Environment where the twigfiddle is stored and will be executed"
           )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try
        {
            $env_id = $input->getArgument('environment-id');
            $this->logger->pushProcessor(function($record) use ($env_id)
            {
                $record['extra']['env_id'] = $env_id;
                return $record;
            });
            $this->logger->info("Started execution.");




            $this->logger->info("Ended execution.");
        }
        catch (\Exception $ex)
        {
            $this->logger->error("An unexpected error occured.", array ('Exception' => $ex));
        }
        $output->write('');
    }

}
