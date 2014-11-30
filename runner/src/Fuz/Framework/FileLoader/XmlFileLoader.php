<?php

namespace Fuz\Framework\FileLoader;

use Symfony\Component\Config\Loader\FileLoader;

class XmlFileLoader extends FileLoader
{

    public function load($resource, $type = null)
    {
        $xml = simplexml_load_file($resource);
        return json_decode(json_encode($xml), 1);
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'xml' === pathinfo(
              $resource, PATHINFO_EXTENSION
        );
    }

}
