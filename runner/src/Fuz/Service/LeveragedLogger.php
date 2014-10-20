<?php

namespace Fuz\Service;

use \Psr\Log\LoggerInterface;
use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;

class LeveragedLogger implements LoggerInterface
{

    protected $loggers;
    protected $stream;

    public function __construct(StreamHandler $stream)
    {
        $this->loggers = array ();
        $this->stream = $stream;
    }

    public function alert($message, array $context = array ())
    {
        $this->log(Monolog::ALERT, $message, $context);
    }

    public function critical($message, array $context = array ())
    {
        $this->log(Monolog::CRITICAL, $message, $context);
    }

    public function debug($message, array $context = array ())
    {
        $this->log(Monolog::DEBUG, $message, $context);
    }

    public function emergency($message, array $context = array ())
    {
        $this->log(Monolog::EMERGENCY, $message, $context);
    }

    public function error($message, array $context = array ())
    {
        $this->log(Monolog::ERROR, $message, $context);
    }

    public function info($message, array $context = array ())
    {
        $this->log(Monolog::INFO, $message, $context);
    }

    public function log($level, $message, array $context = array ())
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[2]['class'];
        if (!array_key_exists($caller, $this->loggers))
        {
            $monolog = new Monolog($caller);
            $monolog->pushHandler($this->stream);
            $this->loggers[$caller] = $monolog;
        }
        $this->loggers[$caller]->log($level, $message, $context);
    }

    public function notice($message, array $context = array ())
    {
        $this->log(Monolog::NOTICE, $message, $context);
    }

    public function warning($message, array $context = array ())
    {
        $this->log(Monolog::WARNING, $message, $context);
    }

}
