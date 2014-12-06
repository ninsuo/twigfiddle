<?php

namespace Fuz\Process\TwigEngine;

interface TwigEngineInterface
{

    public function bootEnvironment($cache_dir);

    public function execute($template, array $context = array ());

    public function getCompiledTemplate($template);

    public function getVersions();

}
