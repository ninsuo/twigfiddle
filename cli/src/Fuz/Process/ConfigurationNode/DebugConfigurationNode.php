<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
                ->booleanNode('allowed')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('directory')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($dir) {
                            return !is_dir($dir) || !is_writeable($dir);
                        })
                        ->thenInvalid("Debug's directory does not exist or is not writeable: %s")
                    ->end()
                ->end()
                ->scalarNode('context_file')
                    ->defaultValue('context.srz')
                    ->validate()
                        ->ifTrue(function ($file) {
                            return strpos($file, '/') !== false;
                        })
                        ->thenInvalid("The context file name can't contain a slash ( / ): %s")
                    ->end()
                ->end()
                ->integerNode('expiry')
                    ->defaultValue(720)
                ->end()
           ->end()
        ;

        return $rootNode;
    }
}
