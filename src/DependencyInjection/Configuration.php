<?php
namespace Yoanbernabeu\AirtableClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('airtable_client');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('key')
                    ->isRequired()
                    ->info('The API key. Please refer to your account settings. See https://support.airtable.com/hc/en-us/articles/219046777-How-do-I-get-my-API-key-')
                ->end()
                ->scalarNode('id')
                    ->isRequired()
                    ->info('The table ID. Please refer to your account settings.')
                ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}
