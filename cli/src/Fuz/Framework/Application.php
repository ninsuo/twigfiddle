<?php

namespace Fuz\Framework;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Console\Application as Console;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Fuz\Framework\Api\ConfigurationNodeInterface;
use Fuz\Framework\Configuration\ApplicationConfiguration;
use Fuz\Framework\Core\MonologContainer;

class Application
{

    protected $container;
    protected $applicationDir;
    protected $rootDir;
    protected $configuration;
    protected $console;

    public function __construct()
    {
        $this->container = new MonologContainer();
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

    public function getContainer()
    {
        return $this->container;
    }

    protected function initRootDir()
    {
        $r = new \ReflectionObject($this);
        $this->applicationDir = realpath(str_replace('\\', '/', dirname($r->getFileName())));
        $this->rootDir = realpath($this->applicationDir . '/../../../');
        $this->container->setParameter('rootDir', $this->rootDir);
        return $this;
    }

    protected function initCoreServices()
    {
        $locator = new FileLocator($this->applicationDir . '/Resources/config');
        $loader = new YamlFileLoader($this->container, $locator);
        $loader->load('services.yml');
        $loader->load('parameters.yml');
        return $this;
    }

    protected function initUserServices()
    {
        $locator = new FileLocator($this->rootDir . "/config/");
        $loader = new YamlFileLoader($this->container, $locator);
        $loader->load('services.yml');
        $loader->load('parameters.yml');
        return $this;
    }

    protected function initConfiguration()
    {
        $dir = $this->rootDir . "/config/";
        $config = $this->container->get('file.loader')->load($dir, 'config.yml');
        $configs = array ($this->container->getParameterBag()->resolveValue($config));
        $processor = new Processor();
        $serviceIds = array_keys($this->container->findTaggedServiceIds('configuration.node'));
        $nodes = array ();
        foreach ($serviceIds as $serviceId)
        {
            $service = $this->container->get($serviceId);
            if ($service instanceof ConfigurationNodeInterface)
            {
                $nodes[] = $service;
            }
        }
        $configuration = new ApplicationConfiguration($nodes);
        $this->configuration = $processor->processConfiguration($configuration, $configs);
        return $this;
    }

    protected function initLogger()
    {
        $dir = $this->rootDir . "/logs/";
        $log = "{$dir}/{$this->configuration['logger']['name']}";
        $max_files = $this->configuration['logger']['max_files'];
        $levels = Logger::getLevels();
        $level = $levels[$this->configuration['logger']['level']];
        $handler = new RotatingFileHandler($log, $max_files, $level);
        $this->container->pushHandler($handler);
        return $this;
    }

    protected function initCommands()
    {
        $this->console = new Console();
        $serviceIds = array_keys($this->container->findTaggedServiceIds('command'));
        foreach ($serviceIds as $serviceId)
        {
            $service = $this->container->get($serviceId);
            $this->console->add($service);
        }
        return $this;
    }

}
