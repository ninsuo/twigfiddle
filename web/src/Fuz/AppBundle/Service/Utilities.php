<?php

namespace Fuz\AppBundle\Service;

use Psr\Log\LoggerInterface;

class Utilities
{

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function randomString($length, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890')
    {
        mt_srand(base_convert(uniqid(), 16, 10));
        $base = strlen($chars);
        $string = '';
        for ($i = 0; ($i < $length); $i++)
        {
            $string .= $chars[mt_rand(0, $base - 1)];
        }
        return $string;
    }

}
