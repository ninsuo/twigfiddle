<?php

namespace Fuz\Process\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Fuz\Framework\Api\ConfigurationNodeInterface;

class DebugConfigurationNode implements ConfigurationNodeInterface
{

    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('debug');

        $rootNode
           ->children()
                ->scalarNode("directory")
                    ->isRequired()
                ->end()
                ->scalarNode("context_file")
                    ->defaultValue('context.srz')
                ->end()
                ->integerNode('expiry')
                    ->defaultValue(720)
                ->end()
           ->end()
        ;

        return $rootNode;
    }

}
