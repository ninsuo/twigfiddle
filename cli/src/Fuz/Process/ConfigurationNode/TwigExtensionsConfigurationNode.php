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

use Fuz\Framework\Api\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class TwigExtensionsConfigurationNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('twig_extensions');

        $rootNode
           ->children()
                ->scalarNode('directory')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($dir) {
                            return !is_dir($dir) || !is_readable($dir) || 0 === count(glob("{$dir}/*"));
                        })
                        ->thenInvalid('Unable to find twig extensions in %s: did you run the installation script?')
                    ->end()
                ->end()
                ->scalarNode('apc_cache_key')
                    ->defaultValue('twigfiddle.twig_extensions')
                ->end()
           ->end()
        ;

        return $rootNode;
    }
}
