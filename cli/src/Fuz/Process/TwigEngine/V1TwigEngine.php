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

class V1TwigEngine extends AbstractTwigEngine
{
    /**
     * Loads Twig using 1.x autoloader.
     *
     * The environment is loaded about the same way in all 1.x Twig versions.
     *
     * The only change was the way the cache directory is declared: at the very beginning, cache
     * directory (in fact, compilation directory) were given in the twig loader. But now, the cache
     * directory is given on Environment's options.
     *
     * @param  string            $sourceDirectory    Twig's source directory
     * @param  string            $cacheDirectory     Cache directory where compiled templates should go
     * @param  string            $executionDirectory Template's directory
     * @return \Twig_Environment
     */
    public function load($sourceDirectory, $cacheDirectory, $executionDirectory)
    {
        require $sourceDirectory.DIRECTORY_SEPARATOR.'/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();

        $twigLoader      = new \Twig_Loader_Filesystem($executionDirectory, $cacheDirectory);
        $twigEnvironment = new \Twig_Environment($twigLoader, array('cache' => $cacheDirectory));

        return $twigEnvironment;
    }

    public function getName()
    {
        return 'Twig v1.x';
    }
}
