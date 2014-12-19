<?php

namespace Fuz\Process\TwigEngine;

interface TwigEngineInterface
{

    public function render($sourceDirectory, $cacheDirectory, $template, array $context = array ());

    public function extractTemplateName($cacheDirectory);
}
