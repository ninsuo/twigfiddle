<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('fuz_app');

        $rootNode
           ->children()
                ->arrayNode('process')
                    ->isRequired()
                    ->children()
                        ->scalarNode('root_dir')
                            ->isRequired()
                            ->validate()
                                ->ifTrue(function ($file) {
                                    return !is_dir($file) || !is_readable($file);
                                })
                                ->thenInvalid("Unable to read Twigfiddle's root directory at: %s")
                            ->end()
                        ->end()
                        ->scalarNode('config_path')
                            ->isRequired()
                            ->validate()
                                ->ifTrue(function ($file) {
                                    return !is_file($file) || !is_readable($file);
                                })
                                ->thenInvalid("Unable to read Twigfiddle's process config file at: %s")
                            ->end()
                        ->end()
                        ->arrayNode('container_file_paths')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')
                                ->validate()
                                    ->ifTrue(function ($file) {
                                        return !is_file($file) || !is_readable($file);
                                    })
                                    ->thenInvalid("Unable to read Twigfiddle's process parameters file at: %s")
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('command')
                            ->isRequired()
                        ->end()
                        ->scalarNode('apc_cache_key')
                            ->defaultValue('twigfiddle.process')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('web')
                    ->isRequired()
                    ->children()
                        ->scalarNode('github_repository_root')
                            ->defaultValue('https://github.com/ninsuo/twigfiddle/blob/master')
                        ->end()
                        ->integerNode('max_fiddles_in_session')
                            ->defaultValue(100)
                        ->end()
                        ->integerNode('default_fiddle_hash_size')
                            ->defaultValue(5)
                        ->end()
                        ->arrayNode('recaptcha')
                            ->isRequired()
                            ->children()
                                ->scalarNode('check_url')
                                    ->defaultValue('https://www.google.com/recaptcha/api/siteverify')
                                ->end()
                                ->scalarNode('post_param')
                                    ->defaultValue('g-recaptcha-response')
                                ->end()
                                ->scalarNode('site_key')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('secret_key')
                                    ->isRequired()
                                ->end()
                                ->arrayNode('sessions_per_ip')
                                    ->isRequired()
                                    ->children()
                                        ->integerNode('max')
                                            ->defaultValue(20)
                                        ->end()
                                        ->integerNode('delay')
                                            ->defaultValue(3600)
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('strategies')
                                    ->defaultValue([])
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        ->children()
                                            ->integerNode('hits')
                                                ->isRequired()
                                            ->end()
                                            ->integerNode('delay')
                                                ->isRequired()
                                            ->end()
                                        ->end()
                                    ->end()
                               ->end()
                           ->end()
                        ->end()
                        ->arrayNode('samples')
                            ->defaultValue([])
                            ->prototype('array')
                                ->prototype('array')
                                    ->children()
                                       ->scalarNode('hash')
                                           ->isRequired()
                                       ->end()
                                       ->integerNode('revision')
                                           ->defaultValue(1)
                                       ->end()
                                       ->scalarNode('label')
                                           ->isRequired()
                                       ->end()
                                   ->end()
                               ->end()
                           ->end()
                       ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
