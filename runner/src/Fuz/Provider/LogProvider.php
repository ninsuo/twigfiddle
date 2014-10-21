<?php

namespace Fuz\Provider;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
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

}
