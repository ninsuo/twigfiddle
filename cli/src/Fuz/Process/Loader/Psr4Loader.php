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
use Twig\Error\RuntimeError;
use Twig\TwigFilter;

class Psr4Loader extends AbstractTwigEngine
{
    /**
     * In latest Twig versions, classes are namespaced.
     *
     * @param string $sourceDirectory    Twig's source directory
     * @param string $cacheDirectory     Cache directory where compiled templates should go
     * @param string $executionDirectory Template's directory
     *
     * @return \Twig\Environment
     */
    public function load($sourceDirectory, $cacheDirectory, $executionDirectory)
    {
        spl_autoload_register(function ($class) use ($sourceDirectory) {
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
        });

        $twigLoader      = new \Twig\Loader\FilesystemLoader($executionDirectory);
        $twigEnvironment = new \Twig\Environment($twigLoader, [
            'cache'            => $cacheDirectory,
            'strict_variables' => $this->agent->getFiddle()->isWithStrictVariables(),
        ]);

        foreach (['filter', 'map', 'sort', 'reduce'] as $filter) {
            $twigEnvironment->addFilter(
                new TwigFilter($filter, function($mixed) use ($filter) {
                    throw new RuntimeError(sprintf('Sorry, filter "%s" is disabled for security reasons.', $filter));
                })
            );
        }

        return $twigEnvironment;
    }
}
