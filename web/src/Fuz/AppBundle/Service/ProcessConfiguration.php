<?php

namespace Fuz\AppBundle\Service;

use Doctrine\Common\Cache\ApcCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

class ProcessConfiguration
{

    protected $localConfig;
    protected $environment;
    protected $remoteConfig;

    public function __construct(array $localConfig, $environment)
    {
        $this->localConfig = $localConfig;
        $this->environment = $environment;
        $this->remoteConfig = null;
    }

    public function getProcessConfig()
    {
        if (!is_null($this->remoteConfig))
        {
            return $this->remoteConfig;
        }

        if ($this->environment === 'prod')
        {
            $apc = new ApcCache();
            $id = $this->localConfig['apc_cache_key'];
            if ($apc->contains($id))
            {
                $this->remoteConfig = $apc->fetch($id);
            }
            else
            {
                $this->remoteConfig = $this->loadRemoteConfig();
                $apc->save($id, $this->remoteConfig);
            }
        }

        return $this->remoteConfig;
    }

    public function loadRemoteConfig()
    {
        $rootDir = $this->localConfig['root_dir'];
        $configFile = $this->localConfig['config_path'];
        $parameterFiles = $this->localConfig['parameters_paths'];

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

        $this->remoteConfig = $config;
        return $config;
    }

    public function getSupportedTwigVersions()
    {
        $cfg = $this->getProcessConfig();
    }

}
