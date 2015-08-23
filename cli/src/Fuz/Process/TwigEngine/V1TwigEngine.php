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
     * Loads Twig using 1.x autoloader
     *
     * @param string $sourceDirectory
     */
    public function load($sourceDirectory)
    {
        require $sourceDirectory.DIRECTORY_SEPARATOR.'/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
    }

    /**
     * From all released versions of Twig, there are backward compatibility to load a template.
     *
     * The only change was the way the cache directory is declared: at the very beginning, cache
     * directory (in fact, compilation directory) were given in the twig loader. But now, the cache
     * directory is given on Environment's options.
     *
     * @param string $cacheDirectory
     * @param string $template
     * @param array  $context
     *
     * @return string
     */
    public function render($cacheDirectory, $template, array $context = array())
    {
        $executionDirectory = dirname($template);
        $mainTemplate = basename($template);

        $twigLoader = new \Twig_Loader_Filesystem($executionDirectory, $cacheDirectory);
        $twigEnvironment = new \Twig_Environment($twigLoader, array('cache' => $cacheDirectory));

        $templateObject = $twigEnvironment->loadTemplate($mainTemplate);

        ob_start();
        $templateObject->display($context);
        $result = ob_get_clean();

        return $result;
    }

    public function getName()
    {
        return 'Twig v1.x';
    }
}
