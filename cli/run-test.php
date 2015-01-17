#!/usr/bin/env php
<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!include __DIR__ . '/vendor/autoload.php')
{
    die('You must set up the project dependencies.');
}

$app = new Fuz\Framework\Application('test');
$app->run();

