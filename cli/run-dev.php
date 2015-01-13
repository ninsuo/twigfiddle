#!/usr/bin/env php
<?php

if (!include __DIR__.'/vendor/autoload.php') {
    die('You must set up the project dependencies.');
}

$app = new Fuz\Framework\Application('dev');
$app->run();
