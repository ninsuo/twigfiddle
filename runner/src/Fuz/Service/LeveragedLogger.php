<?php

namespace Fuz\Service;

use \Psr\Log\LoggerInterface;
use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;
use Fuz\Exception\InvalidLogLevelException;

class LeveragedLogger implements LoggerInterface
{

    protected $enabled;
    protected $loggers;
    protected $stream;

    public function __construct(array &$parameters)
    {
        $this->enabled = true;
        $this->loggers = array ();
        $this->stream = $this->getMonologStream($parameters);
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

    protected function getMonologStream(array &$parameters)
    {
        if (!array_key_exists('level', $parameters))
        {
            $parameters['level'] = 'DEBUG';
        }
        if ((!array_key_exists('path', $parameters)) || (!is_writable($parameters['path'])))
        {
            $this->enabled = false;
        }
        $logLevel = $this->convertLogLevel($parameters['level']);
        return new StreamHandler($parameters['path'], $logLevel);
    }

    protected function convertLogLevel($level)
    {
        switch ($level)
        {
            case 'DEBUG':
                $level = Monolog::DEBUG;
                break;
            case 'INFO':
                $level = Monolog::INFO;
                break;
            case 'NOTICE':
                $level = Monolog::NOTICE;
                break;
            case 'WARNING':
                $level = Monolog::WARNING;
                break;
            case 'ERROR':
                $level = Monolog::ERROR;
                break;
            case 'CRITICAL':
                $level = Monolog::CRITICAL;
                break;
            case 'ALERT':
                $level = Monolog::ALERT;
                break;
            default:
                break;
        }
        if (is_null($level))
        {
            throw new InvalidLogLevelException($level);
        }
        return $level;
    }

}
