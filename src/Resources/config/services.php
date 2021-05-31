<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2020 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableClientInterface;

return static function (ContainerConfigurator $container): void {
    $container = $container->services()->defaults()
        ->public()
        ->autoconfigure()
        ->autowire()
    ;

    $container->set(AirtableClient::class, AirtableClient::class)
        ->args([
            '%yoanbernabeu_airtable_client.airtable_client.key%',
            '%yoanbernabeu_airtable_client.airtable_client.id%',
        ])
    ;

    $container->alias(AirtableClientInterface::class, AirtableClient::class);
};
