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

class XmlFileLoader extends FileLoader
{
    public function load($resource, $type = null)
    {
        $xml = simplexml_load_file($resource, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOWARNING | LIBXML_NOERROR);
        if ($xml === false) {
            $message = 'Unable to parse the given XML input:'.PHP_EOL;
            foreach (libxml_get_errors() as $key => $error) {
                $message .= ($key + 1).': '.trim($error->message, PHP_EOL).PHP_EOL;
            }
            throw new SyntaxErrorException($message);
        }

        return json_decode(json_encode($xml), 1);
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'xml' === pathinfo(
              $resource, PATHINFO_EXTENSION
        );
    }
}
