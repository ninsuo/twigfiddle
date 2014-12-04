<?php

namespace Fuz\Process\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Fuz\Framework\Api\ConfigurationNodeInterface;

class TwigEnginesConfigurationNode implements ConfigurationNodeInterface
{

    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('twig_engines');

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
