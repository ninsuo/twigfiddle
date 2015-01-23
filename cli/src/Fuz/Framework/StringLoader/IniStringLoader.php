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

use Fuz\Framework\StringLoader\StringLoaderInterface;
use Fuz\Framework\Exception\StringLoaderException;

class IniStringLoader implements StringLoaderInterface
{

    public function load($stream)
    {
        $array = parse_ini_string($stream, true, INI_SCANNER_RAW);
        if ($array === false)
        {
            $error = error_get_last();
            throw new StringLoaderException("Unable to parse the given INI input: {$error['message']}.");
        }
        return $array;
    }

}
