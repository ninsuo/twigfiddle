<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Util;

use Doctrine\Common\Cache\ApcCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;
use Psr\Log\LoggerInterface;

class ProcessConfiguration
{

    protected $logger;
    protected $localConfig;
    protected $environment;
    protected $remoteConfig;

    public function __construct(LoggerInterface $logger, array $localConfig, $environment)
    {
        $this->logger = $logger;
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
        else
        {
            $this->remoteConfig = $this->loadRemoteConfig();
        }

        return $this->remoteConfig;
    }

    protected function loadRemoteConfig()
    {
        $rootDir = $this->localConfig['root_dir'];
        $configFile = $this->localConfig['config_path'];
        $containerFiles = $this->localConfig['container_file_paths'];

        $this->logger->info("Reloading process configuration from {$configFile}");

        $sluggedConfig = Yaml::parse(file_get_contents($configFile));

        $processContainer = new ContainerBuilder();

        foreach ($containerFiles as $containerFile)
        {
            $locator = new FileLocator(dirname($containerFile));
            $loader = new Loader\YamlFileLoader($processContainer, $locator);
            $loader->load(basename($containerFile));
        }

        $processContainer->setParameter('env', $this->environment);
        $processContainer->setParameter('root_dir', $rootDir);
        $config = $processContainer->getParameterBag()->resolveValue($sluggedConfig);

        $config['supported_versions'] = $this->getSupportedTwigVersions($processContainer);

        $this->remoteConfig = $config;
        return $config;
    }

    protected function getSupportedTwigVersions(ContainerBuilder $processContainer)
    {
        $versions = array ();

        $engineServiceIds = $processContainer->findTaggedServiceIds('twig.engine');

        foreach ($engineServiceIds as $tags)
        {
            foreach ($tags as $tag)
            {
                if (!array_key_exists('versions', $tag) || !array_key_exists('label', $tag))
                {
                    continue;
                }
                $versions[$tag['label']] = array_map('trim', explode("/", $tag['versions']));
            }
        }

        return $versions;
    }

}
