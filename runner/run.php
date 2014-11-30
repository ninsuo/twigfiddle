#!/usr/bin/env php
<?php

use Fuz\Framework\Application;

if (!include __DIR__ . '/vendor/autoload.php')
{
    die('You must set up the project dependencies.');
}

$app = new Application();
$app->run();

