<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class AirtableTransport extends ScopingHttpClient implements AirtableTransportInterface
{
    public const BASE_URI = 'https://api.airtable.com';
    public const VERSION = 'v0';
    private string $id;

    public function __construct(HttpClientInterface $client, string $id, array $defaultOptionsByRegexp, string $defaultRegexp = null)
    {
        parent::__construct($client, $defaultOptionsByRegexp, $defaultRegexp);

        $this->id = $id;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $url = sprintf('%s/%s/%s', self::VERSION, $this->id, $url);

        return parent::request($method, $url, $options);
    }
}
