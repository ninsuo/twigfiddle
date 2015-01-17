<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework\Core;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;

class MonologContainer extends ContainerBuilder
{

    protected $loggers = array ();
    protected $handlers = array ();
    protected $processors = array ();

    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);
    }

    public function pushHandler(HandlerInterface $handler)
    {
        foreach (array_keys($this->loggers) as $key)
        {
            $this->loggers[$key]->pushHandler($handler);
        }
        array_unshift($this->handlers, $handler);
        return $this;
    }

    public function popHandler()
    {
        if (count($this->handlers) > 0)
        {
            foreach (array_keys($this->loggers) as $key)
            {
                $this->loggers[$key]->popHandler();
            }
            array_shift($this->handlers);
        }
        return $this;
    }

    public function pushProcessor($callback)
    {
        foreach (array_keys($this->loggers) as $key)
        {
            $this->loggers[$key]->pushProcessor($callback);
        }
        array_unshift($this->processors, $callback);
        return $this;
    }

    public function popProcessor()
    {
        if (count($this->processors) > 0)
        {
            foreach (array_keys($this->loggers) as $key)
            {
                $this->loggers[$key]->popProcessor();
            }
            array_shift($this->processors);
        }
        return $this;
    }

    public function getHandlers()
    {
        return $this->handlers;
    }

    public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        $service = parent::get($id, $invalidBehavior);
        return $this->setLogger($id, $service);
    }

    public function setLogger($id, $service)
    {
        if ($service instanceof LoggerAwareInterface)
        {
            if (!array_key_exists($id, $this->loggers))
            {
                $this->loggers[$id] = new Logger($id, $this->handlers, $this->processors);
            }
            $service->setLogger($this->loggers[$id]);
        }
        return $service;
    }

}
