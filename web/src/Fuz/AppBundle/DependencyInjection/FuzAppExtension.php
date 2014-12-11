<?php

namespace Fuz\AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FuzAppExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value)
        {
            if ($key !== 'container_prefix')
            {
                $container->setParameter("{$config['container_prefix']}.{$key}", $value);
            }
        }

        $this->loadProcessConfig($config['process']['root_dir'], $config['process']['config_path'], $config['process']['parameters_paths'], $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function loadProcessConfig($rootDir, $configFile, $parameterFiles, ContainerBuilder $container)
    {
        $sluggedConfig = Yaml::parse($configFile);

        $processContainer = new ContainerBuilder();

        foreach ($parameterFiles as $parameterFile)
        {
            $locator = new FileLocator(dirname($parameterFile));
            $loader = new Loader\YamlFileLoader($processContainer, $locator);
            $loader->load(basename($parameterFile));
        }

        $processContainer->setParameter('env', $container->getParameter('kernel.environment'));
        $processContainer->setParameter('root_dir', $rootDir);
        $config = $processContainer->getParameterBag()->resolveValue($sluggedConfig);

        $container->setParameter('runner', $config);
    }

}
