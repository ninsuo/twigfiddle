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
                ->end()
           ->end()
        ;

        return $rootNode;
    }

}
