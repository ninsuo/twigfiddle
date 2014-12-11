<?php

namespace Fuz\Process\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Fuz\Framework\Api\ConfigurationNodeInterface;

class TwigSourcesConfigurationNode implements ConfigurationNodeInterface
{

    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('twig_sources');

        $rootNode
           ->children()
                ->scalarNode("directory")
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function($dir) {
                            return !is_dir($dir) || !is_readable($dir);
                        })
                        ->thenInvalid("Unable to find twig sources in %s: did you run the installation script?")
                    ->end()
                ->end()
           ->end()
        ;

        return $rootNode;
    }

}
