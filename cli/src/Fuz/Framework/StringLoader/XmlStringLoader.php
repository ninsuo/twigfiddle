<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework\StringLoader;

use Fuz\Framework\Exception\StringLoaderException;

class XmlStringLoader implements StringLoaderInterface
{
    public function load($stream)
    {
        libxml_use_internal_errors(true);
        $rootedStream = "<root>{$stream}</root>";
        $xml          = simplexml_load_string($rootedStream, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOWARNING | LIBXML_NOERROR);
        if ($xml === false) {
            $message = 'Unable to parse the given XML input:'.PHP_EOL;
            foreach (libxml_get_errors() as $key => $error) {
                $message .= ($key + 1).': '.trim($error->message, PHP_EOL).PHP_EOL;
            }
            throw new StringLoaderException($message);
        }

        return json_decode(json_encode($xml), 1);
    }
}
