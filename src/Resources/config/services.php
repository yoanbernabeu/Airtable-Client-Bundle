<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableClientInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $container->services()->defaults()
        ->public()
        ->autoconfigure()
        ->autowire()
        ->set('airtable_client', AirtableClient::class)
        ->args([
            param("yoanbernabeu_airtable_client.airtable_client.key"),
            param("yoanbernabeu_airtable_client.airtable_client.id")
        ])
        ->alias(AirtableClientInterface::class, 'airtable_client');
};
