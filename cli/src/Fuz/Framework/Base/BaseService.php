<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
