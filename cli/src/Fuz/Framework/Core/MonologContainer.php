<?php

namespace Fuz\Framework\Core;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;

class MonologContainer extends ContainerBuilder
{

    protected $handlers = array ();
    protected $loggers = array ();

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
                $this->loggers[$id] = new Logger($id, $this->handlers);
            }
            $service->setLogger($this->loggers[$id]);
        }
        return $service;
    }

}
