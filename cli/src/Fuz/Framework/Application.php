<?php

namespace Fuz\Framework;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Console\Application as Console;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Fuz\Framework\Api\ConfigurationNodeInterface;
use Fuz\Framework\Base\BaseCommand;
use Fuz\Framework\Configuration\ApplicationConfiguration;
use Fuz\Framework\ConfigurationNode\CommandsConfigurationNode;
use Fuz\Framework\ConfigurationNode\LoggerConfigurationNode;
use Fuz\Framework\Core\MonologContainer;

class Application
{

    protected $container;
    protected $applicationDir;
    protected $rootDir;
    protected $configurationNodes;
    protected $configuration;
    protected $console;

    public function __construct()
    {
        $this->container = new MonologContainer();
        $this
           ->initRootDir()
           ->initCoreServices()
           ->initUserServices()
        ;
        $this->configurationNodes = array ();
    }

    public function run()
    {
        $this
           ->initConfiguration()
           ->initCommands()
           ->initLogger()
           ->initCommands()
        ;
        $this->console->run();
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function pushConfigurationNode(ConfigurationNodeInterface $node)
    {
        $this->configurationNodes[] = $node;
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
        $configuration = new ApplicationConfiguration(array_merge(array (
                   new CommandsConfigurationNode(),
                   new LoggerConfigurationNode(),
              ), $this->configurationNodes));
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
        foreach ($this->configuration['commands'] as $path)
        {
            $this->console = new Console();
            foreach (glob($path) as $class)
            {
                $class = str_replace('/', '\\', substr($class, strlen($this->rootDir . '/src/'), -4));
                $command = new $class();
                if ($command instanceof BaseCommand)
                {
                    $command->setContainer($this->container);
                    $this->container->setLogger($command->getName(), $command);
                }
                $this->console->add($command);
            }
        }
        return $this;
    }

}
