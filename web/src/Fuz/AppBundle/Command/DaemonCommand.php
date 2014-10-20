<?php

namespace Fuz\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DaemonCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();
        $this
           ->setName("exec:daemon")
           ->setDescription("Executes a command as daemon")
           ->addArgument('cmd', InputArgument::REQUIRED, "Linux command to execute")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $input->getArgument('cmd');
        $daemon = $this->getContainer()->get('app.daemon');
        $daemon->exec($command);
        return 0;
    }

}
