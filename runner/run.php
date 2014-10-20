<?php

if (!$loader = include __DIR__.'/vendor/autoload.php') {
    die('You must set up the project dependencies.');
}

$app = new Cilex\Application('Cilex');
$app->command(new Fuz\Command\ProcessCommand());
$app->run();
