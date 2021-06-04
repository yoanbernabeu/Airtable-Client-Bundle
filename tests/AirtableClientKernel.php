<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yoanbernabeu\AirtableClientBundle\AirtableClientBundle;

class AirtableClientKernel extends Kernel
{
    use MicroKernelTrait;

    public const KEY = 'dummy key';
    public const ID = 4242;

    public function registerBundles(): array
    {
        return [
            new AirtableClientBundle(),
            new FrameworkBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader): void
    {
        // Load test purpose configuration
        $container->extension('airtable_client', [
            'key' => self::KEY,
            'id' => self::ID,
        ]);

        // Replace HttpClientInterface by a test-friendly HttpClient MockHttpClient
        $container->services()->set(HttpClientInterface::class, MockHttpClient::class);
    }
}
