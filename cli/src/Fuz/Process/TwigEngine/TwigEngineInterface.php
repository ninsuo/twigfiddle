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

    public function render($sourceDirectory, $cacheDirectory, $template, array $context = array ());

    public function extractTemplateName($cacheDirectory);

    public function getName();
}
