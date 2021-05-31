<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;

return static function (ContainerConfigurator $container): void {
    $container = $container->services()->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
    ;

    $container->set(AirtableClient::class)
        ->args([
            '%yoanbernabeu_airtable_client.airtable_client.key%',
            '%yoanbernabeu_airtable_client.airtable_client.id%',
        ])
        ->alias('yoanbernabeu_airtable_client.airtable_client')
    ;
};
