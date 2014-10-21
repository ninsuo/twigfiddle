<?php

namespace Fuz\Base;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseCommand extends Command implements ContainerAwareInterface
{

    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}