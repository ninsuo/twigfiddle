<?php

namespace Fuz\Framework;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Console\Application as Console;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Fuz\Framework\Core\MonologContainer;
use Fuz\Framework\Configuration\ApplicationConfiguration;
use Fuz\Framework\Base\BaseCommand;

class Application
{

    protected $container;
    protected $appDir;
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

    protected function initRootDir()
    {
        $r = new \ReflectionObject($this);
        $this->appDir = realpath(str_replace('\\', '/', dirname($r->getFileName())));
        $this->rootDir = realpath($this->appDir . '/../../../');
        $this->container->setParameter('rootDir', $this->rootDir);
        return $this;
    }

    protected function initCoreServices()
    {
        $locator = new FileLocator($this->appDir . '/Resources/config');
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
        $configuration = new ApplicationConfiguration();
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
            foreach (glob($this->rootDir . '/' . $path) as $class)
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

    public function run()
    {
        $this->console->run();
    }

}
