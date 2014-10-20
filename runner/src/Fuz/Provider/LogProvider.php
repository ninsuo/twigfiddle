<?php

namespace Fuz\Provider;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Fuz\Exception\InvalidLogLevelException;
use Fuz\Service\LeveragedLogger;

class LogProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $logger = new Logger('name');
        $logLevel = $this->convertLogLevel($app['config']['monolog']['level']);
        $stream = new StreamHandler($app['config']['monolog']['path'], $logLevel);
        $logger->pushHandler($stream);
        $app['logger'] = $app->share(function($c) use ($stream) {
            return new LeveragedLogger($stream);
        });
    }

    protected function convertLogLevel($level)
    {
        switch ($level)
        {
            case 'DEBUG':
                $level = Logger::DEBUG;
                break;
            case 'INFO':
                $level = Logger::INFO;
                break;
            case 'NOTICE':
                $level = Logger::NOTICE;
                break;
            case 'WARNING':
                $level = Logger::WARNING;
                break;
            case 'ERROR':
                $level = Logger::ERROR;
                break;
            case 'CRITICAL':
                $level = Logger::CRITICAL;
                break;
            case 'ALERT':
                $level = Logger::ALERT;
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
