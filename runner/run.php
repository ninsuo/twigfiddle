<?php

if (!$loader = include __DIR__.'/vendor/autoload.php') {
    die('You must set up the project dependencies.');
}

$app = new Cilex\Application('Runner');

$app->register(new \Cilex\Provider\ConfigServiceProvider(), array('config.path' => __DIR__ . '/app/config.yml'));
$app->register(new \Fuz\Provider\LogProvider());
$app->command(new Fuz\Command\ProcessCommand());
$app->run();

/*

$app = new Fuz\Application();
$app->container['service'];
$app->container['param'];

$app->registerService()

 */