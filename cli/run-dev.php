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
    echo 'You must set up the project dependencies.' . PHP_EOL;
    return;
}

$app = new Fuz\Framework\Application('dev');
$app->run();

