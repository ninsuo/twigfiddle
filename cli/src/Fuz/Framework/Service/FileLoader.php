<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework\Service;

use Fuz\Framework\FileLoader\JsonFileLoader;
use Fuz\Framework\FileLoader\PhpFileLoader;
use Fuz\Framework\FileLoader\XmlFileLoader;
use Fuz\Framework\FileLoader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

class FileLoader
{
    public function load($dir, $file)
    {
        if (is_scalar($dir)) {
            return $this->load([$dir], $file);
        }

        $locator = new FileLocator($dir);
        $path    = $locator->locate($file);

        $loaderResolver = new LoaderResolver([
                new YamlFileLoader($locator),
                new XmlFileLoader($locator),
                new JsonFileLoader($locator),
                new PhpFileLoader($locator),
        ]);

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $content = $delegatingLoader->load($path);

        return $content;
    }
}
