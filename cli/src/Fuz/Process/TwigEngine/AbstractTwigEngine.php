<?php

namespace Fuz\Process\TwigEngine;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\TwigEngine\TwigEngineInterface;

abstract class AbstractTwigEngine extends BaseService implements TwigEngineInterface
{

    public function render($sourceDirectory, $cacheDirectory, $template, array $context = array ())
    {
        require($sourceDirectory . DIRECTORY_SEPARATOR . '/lib/Twig/Autoloader.php');
        \Twig_Autoloader::register();

        $executionDirectory = dirname($template);
        $mainTemplate = basename($template);

        $twigLoader = new \Twig_Loader_Filesystem($executionDirectory, $cacheDirectory);
        $twigEnvironment = new \Twig_Environment($twigLoader);

        $templateObject = $twigEnvironment->loadTemplate($mainTemplate);

        ob_start();
        $templateObject->display($context);
        $result = ob_get_clean();

        return $result;
    }

    public function extract($cacheDirectory)
    {

    }

}
