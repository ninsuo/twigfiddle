<?php

namespace Fuz\Framework\FileLoader;

use Symfony\Component\Config\Loader\FileLoader;

class PhpFileLoader extends FileLoader
{

    public function load($resource, $type = null)
    {
        return eval(file_get_contents($resource));
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo(
              $resource, PATHINFO_EXTENSION
        );
    }

}
