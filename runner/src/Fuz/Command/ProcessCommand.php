<?php

namespace Fuz\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fuz\Base\BaseCommand;

class ProcessCommand extends BaseCommand
{

    protected function configure()
    {
        parent::configure();
        $this
           ->setName("twigfiddle:run")
           ->setDescription("Executes a twigfiddle (from already prepared environment)")
           ->addArgument('environment-id', InputArgument::REQUIRED,
              "Environment where the twigfiddle is stored and will be executed")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env_id = $input->getArgument('environment-id');

        $this->container->get('logger')->info("test!");

        $output->writeln($env_id);
    }

}
