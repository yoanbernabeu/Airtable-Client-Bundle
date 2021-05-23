<?php
namespace Yoanbernabeu\AirtableClientBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AirtableClientExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Ressources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        
        $definition = $container->getDefinition('yoanbernabeu_airtable_client.airtable_client');
        $definition->setArgument(0, $config['key']);
        $definition->setArgument(1, $config['id']);
    }

    public function getAlias()
    {
        return 'airtable_client';
    }
}
