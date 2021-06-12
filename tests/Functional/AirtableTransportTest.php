<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Yoanbernabeu\AirtableClientBundle\AirtableTransport;

/**
 * @internal
 */
class AirtableTransportTest extends KernelTestCase
{
    /**
     * @test
     */
    public function airtableClientIsWellConfigured(): void
    {
        self::bootKernel();

        $airtableTransport = self::$kernel->getContainer()->get('airtable_transport');

        /** @var MockResponse $response */
        $response = $airtableTransport->request('GET', $path = 'foo');

        $configuredOptions = $response->getRequestOptions();

        static::assertEquals([
            'Authorization: Bearer dummy key',
            'Accept: application/json',
        ], $configuredOptions['headers'], 'Inspect headers is well configured');

        $expectedUrl = sprintf('%s/%s/%s/%s', AirtableTransport::BASE_URI, AirtableTransport::VERSION, self::$kernel->getContainer()->getParameter('yoanbernabeu_airtable_client.airtable_client.id'), $path);
        static::assertEquals('https:', $configuredOptions['base_uri']['scheme'], 'Inspect scheme');
        static::assertEquals('//api.airtable.com', $configuredOptions['base_uri']['authority'], 'Inspect authority');
        static::assertEquals($expectedUrl, $response->getInfo('url'), 'Inspect url');
    }
}
