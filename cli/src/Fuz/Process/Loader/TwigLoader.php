<?php
/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\Loader;

use Fuz\Process\TwigEngine\AbstractTwigEngine;

class TwigLoader extends AbstractTwigEngine
{
    /**
     * Between versions 0.9.0 and 1.37.1, Twig embedded his own autoloader.
     *
     * @param string $sourceDirectory    Twig's source directory
     * @param string $cacheDirectory     Cache directory where compiled templates should go
     * @param string $executionDirectory Template's directory
     *
     * @return \Twig_Environment
     */
    public function load($sourceDirectory, $cacheDirectory, $executionDirectory)
    {
        require $sourceDirectory.DIRECTORY_SEPARATOR.'/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();

        $twigLoader      = new \Twig_Loader_Filesystem($executionDirectory, $cacheDirectory);
        $twigEnvironment = new \Twig_Environment($twigLoader, [
            'cache'            => $cacheDirectory,
            'strict_variables' => $this->agent->getFiddle()->isWithStrictVariables(),
        ]);

        return $twigEnvironment;
    }
}
