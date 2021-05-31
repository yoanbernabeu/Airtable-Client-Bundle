<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Yoanbernabeu\AirtableClientBundle\AirtableClientBundle;

class AirtableClientKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new AirtableClientBundle(),
            new FrameworkBundle()
        ];
    }

    protected function configureContainer(ContainerConfigurator  $container, LoaderInterface $loader): void
    {
        $container->extension('airtable_client', [
            "key" => "key",
            "id" => "id"
        ]);
    }
}
