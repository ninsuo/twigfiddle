#!/usr/bin/env php
<?php

if (!include __DIR__ . '/vendor/autoload.php')
{
    die('You must set up the project dependencies.');
}

use Fuz\Framework\Application;

$app = new Application();
$app->run();
