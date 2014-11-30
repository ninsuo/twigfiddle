<?php

namespace Fuz\Framework\Base;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class BaseService implements LoggerAwareInterface
{

    protected $logger;

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

}