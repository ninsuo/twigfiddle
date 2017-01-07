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

class V2TwigEngine extends AbstractTwigEngine
{
    /**
     * In Twig 2.x, there are no more custom Twig autoloader as Twig
     * should be managed using Composer. But of course, twigfiddle
     * cannot handle it using Composer as it need to support several
     * Twig versions.
     *
     * This autloader is gently provided by @sarciszewski on GitHub:
     * https://github.com/twigphp/Twig/issues/1646#issuecomment-78349076
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
            }
        });

        $twigLoader = new \Twig_Loader_Filesystem($executionDirectory);
        $twigEnvironment = new \Twig_Environment($twigLoader, array(
            'cache' => $cacheDirectory,
            'strict_variables' => $this->agent->getFiddle()->isWithStrictVariables(),
        ));

        return $twigEnvironment;
    }

    public function getName()
    {
        return 'Twig v2.x';
    }
}
