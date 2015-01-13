<?php

namespace Fuz\Framework\StringLoader;

use Fuz\Framework\Exception\StringLoaderException;

class IniStringLoader implements StringLoaderInterface
{
    public function load($stream)
    {
        $array = @parse_ini_string($stream, true, INI_SCANNER_RAW);
        if ($array === false) {
            $error = error_get_last();
            throw new StringLoaderException("Unable to parse the given INI input: {$error['message']}.");
        }

        return $array;
    }
}
