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

class JsonStringLoader implements StringLoaderInterface
{
    public static $errors = [
            JSON_ERROR_NONE             => 'No error has occurred',
            JSON_ERROR_DEPTH            => 'The maximum stack depth has been exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR        => 'Control character error, possibly incorrectly encoded',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded',
            JSON_ERROR_RECURSION        => 'One or more recursive references in the value to be encoded',
            JSON_ERROR_INF_OR_NAN       => 'One or more NAN or INF values in the value to be encoded ',
            JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was givens',
    ];

    public function load($stream)
    {
        if (strtolower($stream) === 'null') {
            return [];
        }
        $array = json_decode($stream, true, 512, JSON_BIGINT_AS_STRING);
        if ($array === null) {
            $error = json_last_error();
            if (!array_key_exists($error, self::$errors)) {
                throw new StringLoaderException('Unable to parse the given JSON input.');
            }
            throw new StringLoaderException(sprintf("Unable to parse the given JSON input: %s\n", self::$errors[$error]));
        }

        return $array;
    }
}
