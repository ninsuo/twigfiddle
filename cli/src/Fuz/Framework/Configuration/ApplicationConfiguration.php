<?php

namespace Fuz\Framework\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Fuz\Framework\ConfigurationNode\CommandsConfigurationNode;
use Fuz\Framework\ConfigurationNode\LoggerConfigurationNode;

class ApplicationConfiguration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        $objects = array(
                new CommandsConfigurationNode(),
                new LoggerConfigurationNode(),
        );

        foreach ($objects as $object)
        {
            $rootNode->append($object->getConfigurationNode());
        }

        return $treeBuilder;
    }

}
