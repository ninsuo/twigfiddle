<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework\FileLoader;

use Symfony\Component\Config\Loader\FileLoader;

class PhpFileLoader extends FileLoader
{
    public function load($resource, $type = null)
    {
        return include $resource;
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo(
              $resource, PATHINFO_EXTENSION
        );
    }
}
