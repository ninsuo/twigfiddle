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

class EnvironmentConfigurationNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('environment');

        $rootNode
           ->children()
                ->scalarNode('directory')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($dir) {
                            return !is_dir($dir) || !is_writeable($dir);
                        })
                        ->thenInvalid("Environment's directory does not exist or is not writeable: %s")
                    ->end()
                ->end()
                ->scalarNode('validation')
                    ->defaultValue('^[a-zA-Z0-9-]{4,16}$')
                    ->validate()
                        ->ifTrue(function ($expr) {
                            return preg_match("/{$expr}/", '/') === true;
                        })
                        ->thenInvalid('Environment validation must reject names containing slashs ( / ): current expresion %s is too permissive.')
                    ->end()
                ->end()
                ->integerNode('expiry')
                    ->defaultValue(24)
                ->end()
           ->end()
        ;

        return $rootNode;
    }
}
