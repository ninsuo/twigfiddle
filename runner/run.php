#!/usr/bin/env php
<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Console\Application;
use Fuz\Base\BaseCommand;

if (!include __DIR__ . '/vendor/autoload.php')
{
    die('You must set up the project dependencies.');
}

date_default_timezone_set('Europe/Paris');

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/app/'));
$loader->load('services.yml');
$loader->load('parameters.yml');

$application = new Application();
foreach (glob(__DIR__ . '/src/Fuz/Command/*') as $class)
{
    $class = str_replace('/', '\\', substr($class, strlen(__DIR__ . '/src/'), -4));
    $command = new $class();
    if ($command instanceof BaseCommand)
    {
        $command->setContainer($container);
    }
    $application->add($command);
}
$application->run();
