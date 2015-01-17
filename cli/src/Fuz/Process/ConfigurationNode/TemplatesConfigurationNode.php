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
                    ->validate()
                        ->ifTrue(function($expr) {
                            return preg_match("/{$expr}/", '/') === true;
                        })
                        ->thenInvalid("Template names expression must reject names containing slashs ( / ): current expresion %s is too permissive.")
                    ->end()
                ->end()
           ->end()
        ;

        return $rootNode;
    }

}
