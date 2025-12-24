<?php

declare(strict_types=1);

namespace Linderp\SuluIndexNowBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sulu_index_now');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('key')
            ->end()
            ->arrayNode('search_engines')
                ->useAttributeAsKey('name')
                ->scalarPrototype()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
