<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework;

use Fuz\Framework\Api\ConfigurationNodeInterface;
use Fuz\Framework\Configuration\ApplicationConfiguration;
use Fuz\Framework\Core\MonologContainer;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application
{
    protected $environment;
    protected $container;
    protected $applicationDir;
    protected $rootDir;
    protected $console;

    public function __construct($environment = 'prod')
    {
        $this->environment = $environment;
        $this->container   = new MonologContainer();
        $this->container->setParameter('env', $environment);

        $this
           ->initRootDir()
           ->initCoreServices()
           ->initUserServices()
           ->initConfiguration()
           ->initCommands()
           ->initLogger()
           ->initCommands()
        ;
    }

    public function run()
    {
        $this->console->run();
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getRootDir()
    {
        return $this->rootDir;
    }

    protected function initRootDir()
    {
        $r                    = new \ReflectionObject($this);
        $this->applicationDir = realpath(str_replace('\\', '/', dirname($r->getFileName())));
        $this->rootDir        = realpath($this->applicationDir.'/../../../');
        $this->container->setParameter('root_dir', $this->rootDir);

        return $this;
    }

    protected function initCoreServices()
    {
        $locator = new FileLocator($this->applicationDir.'/Resources/config');
        $loader  = new YamlFileLoader($this->container, $locator);
        $loader->load('services.yml');

        return $this;
    }

    protected function initUserServices()
    {
        $locator = new FileLocator($this->rootDir.'/config/');
        $loader  = new YamlFileLoader($this->container, $locator);
        $loader->load('services.yml');
        $loader->load("parameters.{$this->environment}.yml");

        return $this;
    }

    protected function initConfiguration()
    {
        $dir        = $this->rootDir.'/config/';
        $config     = $this->container->get('file_loader')->load($dir, 'config.yml');
        $configs    = [$this->container->getParameterBag()->resolveValue($config)];
        $serviceIds = array_keys($this->container->findTaggedServiceIds('configuration.node'));
        $nodes      = [];
        foreach ($serviceIds as $serviceId) {
            $service = $this->container->get($serviceId);
            if ($service instanceof ConfigurationNodeInterface) {
                $nodes[] = $service;
            }
        }
        $processor     = new Processor();
        $configuration = new ApplicationConfiguration($nodes);
        foreach ($processor->processConfiguration($configuration, $configs) as $name => $value) {
            $this->container->setParameter("config.{$name}", $value);
        }

        return $this;
    }

    protected function initLogger()
    {
        $config    = $this->container->getParameter('config.logger');
        $dir       = $this->rootDir.'/logs/';
        $log       = "{$dir}/{$config['name']}";
        $max_files = $config['max_files'];
        $levels    = Logger::getLevels();
        $level     = $levels[$config['level']];
        $handler   = new RotatingFileHandler($log, $max_files, $level);
        $this->container->pushHandler($handler);

        return $this;
    }

    protected function initCommands()
    {
        $this->console = new Console();
        $serviceIds    = array_keys($this->container->findTaggedServiceIds('command'));
        foreach ($serviceIds as $serviceId) {
            $command = $this->container->get($serviceId);
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
            }
            $this->console->add($command);
        }

        return $this;
    }
}
