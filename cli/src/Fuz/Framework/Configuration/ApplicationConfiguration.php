<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ApplicationConfiguration implements ConfigurationInterface
{
    protected $nodes;

    public function __construct(array $nodes = array())
    {
        $this->nodes = $nodes;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        foreach ($this->nodes as $object) {
            $rootNode->append($object->getConfigurationNode());
        }

        return $treeBuilder;
    }
}
