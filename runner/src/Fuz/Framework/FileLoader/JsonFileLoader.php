<?php

namespace Fuz\Framework\FileLoader;

use Symfony\Component\Config\Loader\FileLoader;

class JsonFileLoader extends FileLoader
{

    public function load($resource, $type = null)
    {
        return json_decode(file_get_contents($resource), true);
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'json' === pathinfo(
              $resource, PATHINFO_EXTENSION
        );
    }

}
