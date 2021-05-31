<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableClientInterface;

return static function (ContainerConfigurator $container): void {
    $container->services()->defaults()
        ->public()
        ->autoconfigure()
        ->autowire()
        ->set('airtable_client', AirtableClient::class)
        ->args([
            '%yoanbernabeu_airtable_client.airtable_client.key%',
            '%yoanbernabeu_airtable_client.airtable_client.id%'
        ])
        ->alias(AirtableClientInterface::class, 'airtable_client');
};
