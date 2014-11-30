<?php

namespace Fuz\Framework\FileLoader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader extends FileLoader
{

    public function load($resource, $type = null)
    {
        $data = Yaml::parse(file_get_contents($resource));
        return Yaml::parse($resource);
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
              $resource, PATHINFO_EXTENSION
        );
    }

}
