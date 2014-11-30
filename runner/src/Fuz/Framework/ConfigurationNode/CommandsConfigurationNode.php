<?php

namespace Fuz\Framework\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Fuz\Framework\Api\ConfigurationNodeInterface;

class CommandsConfigurationNode implements ConfigurationNodeInterface
{

    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('commands');

        $rootNode
           ->prototype('scalar')
           ->end()
        ;

        return $rootNode;
    }

}
