<?php

namespace Fuz\Process\Loader;

use Fuz\Process\TwigEngine\AbstractTwigEngine;

class Psr0Loader extends AbstractTwigEngine
{
    /**
     * Between Twig versions 1.38.0 and all further 1.x versions,
     * Twig had no more a custom autoloader, and Twig classes were being
     * namespaced.
     *
     * @param string $sourceDirectory    Twig's source directory
     * @param string $cacheDirectory     Cache directory where compiled templates should go
     * @param string $executionDirectory Template's directory
     *
     * @return \Twig_Environment
     */
    public function load($sourceDirectory, $cacheDirectory, $executionDirectory)
    {
        spl_autoload_register(function ($class) use ($sourceDirectory) {
            $prefix = 'Twig';
            $base_dir = $sourceDirectory.'/lib/Twig/';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $relative_class = substr($class, $len);
            $file = $base_dir.str_replace('_', '/', $relative_class).'.php';
            if (file_exists($file)) {
                require $file;
            } else {
                $prefix = 'Twig\\';
                $base_dir = $sourceDirectory.'/src/';

                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    return;
                }

                $relative_class = substr($class, $len);
                $file = $base_dir.str_replace('\\', '/', $relative_class).'.php';

                if (file_exists($file)) {
                    require $file;
                }
            }
        });

        $twigLoader      = new \Twig_Loader_Filesystem($executionDirectory);
        $twigEnvironment = new \Twig_Environment($twigLoader, [
            'cache'            => $cacheDirectory,
            'strict_variables' => $this->agent->getFiddle()->isWithStrictVariables(),
        ]);

        return $twigEnvironment;
    }
}