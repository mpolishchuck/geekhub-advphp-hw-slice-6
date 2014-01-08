<?php

namespace PaulMaxwell\BlogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('paul_maxwell_blog');
        $rootNode->children()
            ->arrayNode('main_menu')
                ->prototype('array')
                ->children()
                    ->scalarNode('route')->end()
                    ->scalarNode('text')->end()
                ->end()
            ->end()->end()
            ->scalarNode('images_location')->defaultValue('%kernel.root_dir%/../web')->end();

        return $treeBuilder;
    }
}
