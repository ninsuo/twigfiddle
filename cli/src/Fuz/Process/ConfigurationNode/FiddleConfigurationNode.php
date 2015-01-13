<?php

namespace Fuz\Process\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Fuz\Framework\Api\ConfigurationNodeInterface;

class FiddleConfigurationNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fiddle');

        $rootNode
           ->children()
                ->scalarNode("file")
                    ->defaultValue('fiddle.shr')
                    ->validate()
                        ->ifTrue(function ($file) {
                            return strpos($file, '/') !== false;
                        })
                        ->thenInvalid("The shared memory file name can't contain a slash ( / ): %s")
                    ->end()
                ->end()
                ->scalarNode('templates_dir')
                    ->defaultValue('twig')
                    ->validate()
                        ->ifTrue(function ($dir) {
                            return strpos($dir, '/') !== false;
                        })
                        ->thenInvalid("The template directory's name can't contain a slash ( / ): %s")
                    ->end()
                ->end()
                ->scalarNode('compiled_dir')
                    ->defaultValue('php')
                    ->validate()
                        ->ifTrue(function ($dir) {
                            return strpos($dir, '/') !== false;
                        })
                        ->thenInvalid("The compiled files directory's name can't contain a slash ( / ): %s")
                    ->end()
                ->end()
           ->end()
        ;

        return $rootNode;
    }
}
