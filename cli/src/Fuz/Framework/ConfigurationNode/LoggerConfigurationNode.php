<?php

namespace Fuz\Framework\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Monolog\Logger;
use Fuz\Framework\Api\ConfigurationNodeInterface;

class LoggerConfigurationNode implements ConfigurationNodeInterface
{

    public function getConfigurationNode()
    {
        $levels = array_keys(Logger::getLevels());
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('logger');
        $rootNode
           ->children()
                ->enumNode("level")
                    ->values($levels)
                    ->defaultValue(reset($levels))
                ->end()
                ->integerNode('max_files')
                    ->defaultValue(30)
                ->end()
                ->scalarNode('name')
                    ->defaultValue('app.log')
                ->end()
           ->end()
        ;
        return $rootNode;
    }

}
