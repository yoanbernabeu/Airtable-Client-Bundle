<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableClientInterface;

/**
 * @internal
 */
class AirtableClientTest extends KernelTestCase
{
    /**
     * @test
     */
    public function ifKernelBootingAndAirtableClientIsRegistered(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        static::assertTrue($container->has(AirtableClientInterface::class));
        static::assertTrue($container->has('airtable_client'));
        static::assertInstanceOf(AirtableClient::class, $container->get('airtable_client'));
    }
}
