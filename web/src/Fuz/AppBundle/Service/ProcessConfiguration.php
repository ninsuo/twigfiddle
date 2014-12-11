<?php

namespace Fuz\AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

class ProcessConfiguration
{

    protected $twigfiddleConfig;
    protected $environment;
    protected $processConfig;

    public function __construct(array $twigfiddleConfig, $environment)
    {
        $this->twigfiddleConfig = $twigfiddleConfig;
        $this->environment = $environment;
        $this->processConfig = null;
    }

    public function getProcessConfig()
    {
        if (!is_null($this->processConfig))
        {
            return $this->processConfig;
        }

        $rootDir = $this->twigfiddleConfig['root_dir'];
        $configFile = $this->twigfiddleConfig['config_path'];
        $parameterFiles = $this->twigfiddleConfig['parameters_paths'];

        $sluggedConfig = Yaml::parse($configFile);

        $processContainer = new ContainerBuilder();

        foreach ($parameterFiles as $parameterFile)
        {
            $locator = new FileLocator(dirname($parameterFile));
            $loader = new Loader\YamlFileLoader($processContainer, $locator);
            $loader->load(basename($parameterFile));
        }

        $processContainer->setParameter('env', $this->environment);
        $processContainer->setParameter('root_dir', $rootDir);
        $config = $processContainer->getParameterBag()->resolveValue($sluggedConfig);

        $this->processConfig = $config;
        return $config;
    }

}
