<?php

namespace Fuz\Exception;

class InvalidLogLevelException extends \Exception
{

    public function __construct($logLevel, $code = 0, $previous = null)
    {
        parent::__construct(sprintf("Invalid log level: %s\n", $logLevel), $code, $previous);
    }

}
