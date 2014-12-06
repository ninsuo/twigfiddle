<?php

namespace Fuz\Process\TwigEngine;

interface TwigEngineInterface
{

    public function render($cacheDirectory, $template, array $context = array ());

}
