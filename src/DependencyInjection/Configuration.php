<?php
namespace Yoanbernabeu\AirtableClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('airtable_client');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('key')->end()
                ->scalarNode('id')->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}
