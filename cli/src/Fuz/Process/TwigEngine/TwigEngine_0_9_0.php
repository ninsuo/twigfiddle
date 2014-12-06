<?php

namespace Fuz\Process\TwigEngine;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\TwigEngine\TwigEngineInterface;
use Fuz\Process\Entity\Result;

class TwigEngine_0_9_0 extends BaseService implements TwigEngineInterface
{

    public function render($cacheDirectory, $template, array $context = array ())
    {
        $result = new Result();


        return $result;
    }

}