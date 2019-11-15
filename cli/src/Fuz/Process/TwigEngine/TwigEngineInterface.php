<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\TwigEngine;

interface TwigEngineInterface
{
    public function load($sourceDirectory, $cacheDirectory, $executionDirectory);

    public function render($environment, $template, array $context = []);

    public function extractTemplateName($cacheDirectory);
}
