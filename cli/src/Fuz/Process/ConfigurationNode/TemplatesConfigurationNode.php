<?php

namespace Fuz\Process\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Fuz\Framework\Api\ConfigurationNodeInterface;

class TemplatesConfigurationNode implements ConfigurationNodeInterface
{

    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('templates');

        $rootNode
           ->children()
                ->scalarNode("validation")
                    ->defaultValue('^[A-Za-z0-9-_]{1,16}\\.twig$')
                ->end()
           ->end()
        ;

        return $rootNode;
    }

}
