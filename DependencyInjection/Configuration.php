<?php

declare(strict_types=1);

namespace Linderp\SuluIndexNowBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sulu_index_now');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        /** @var NodeBuilder $children */
        $children = $rootNode->children();
        $children->scalarNode('key');
        /** @var ArrayNodeDefinition $searchEngines */
        $searchEngines = $children->arrayNode('search_engines');
        $searchEngines->useAttributeAsKey('name');
        $searchEngines->scalarPrototype();

        return $treeBuilder;
    }
}
