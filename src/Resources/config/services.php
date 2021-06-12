<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableClientInterface;
use Yoanbernabeu\AirtableClientBundle\AirtableTransport;
use Yoanbernabeu\AirtableClientBundle\AirtableTransportInterface;

return static function (ContainerConfigurator $container): void {
    $scopeConfig = [
        'base_uri' => AirtableTransport::BASE_URI,
        'headers' => [
            'Authorization' => 'Bearer '.param('yoanbernabeu_airtable_client.airtable_client.key'),
            'Accept' => 'application/json',
        ],
    ];

    $container->services()->defaults()
        ->public()
        ->autoconfigure()
        ->autowire()
            ->set('airtable_client', AirtableClient::class)
                ->args([service('airtable_transport')])
            ->alias(AirtableClientInterface::class, 'airtable_client')

            ->set('airtable_transport', AirtableTransport::class)
                ->arg('$id', param('yoanbernabeu_airtable_client.airtable_client.id'))
                ->arg('$defaultOptionsByRegexp', [$scopeConfig['base_uri'] => $scopeConfig])
                ->arg('$defaultRegexp', $scopeConfig['base_uri'])
            ->alias(AirtableTransportInterface::class, 'airtable_transport')
    ;
};
