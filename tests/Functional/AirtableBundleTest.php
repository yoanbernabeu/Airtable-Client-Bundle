<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableClientInterface;
use Yoanbernabeu\AirtableClientBundle\AirtableTransport;
use Yoanbernabeu\AirtableClientBundle\AirtableTransportInterface;
use Yoanbernabeu\AirtableClientBundle\Tests\AirtableClientKernel;

/**
 * @internal
 */
final class AirtableBundleTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @test
     */
    public function ifKernelBootingAndAirtableClientIsRegistered(): void
    {
        static::assertTrue(self::$kernel->getContainer()->has(AirtableClientInterface::class));
        static::assertTrue(self::$kernel->getContainer()->has('airtable_client'));
        static::assertInstanceOf(AirtableClient::class, self::$kernel->getContainer()->get('airtable_client'));

        static::assertTrue(self::$kernel->getContainer()->has(AirtableTransportInterface::class));
        static::assertTrue(self::$kernel->getContainer()->has('airtable_transport'));
        static::assertInstanceOf(AirtableTransport::class, self::$kernel->getContainer()->get('airtable_transport'));
    }

    /**
     * @test
     */
    public function bundleIsWellConfigured(): void
    {
        static::assertEquals(AirtableClientKernel::ID, self::$kernel->getContainer()->getParameter('yoanbernabeu_airtable_client.airtable_client.id'));
        static::assertEquals(AirtableClientKernel::KEY, self::$kernel->getContainer()->getParameter('yoanbernabeu_airtable_client.airtable_client.key'));
    }
}
