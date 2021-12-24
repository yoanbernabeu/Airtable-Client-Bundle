<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface AirtableTransportInterface extends HttpClientInterface
{
    /**
     * Requests an HTTP resource to AirTable Metadata API.
     *
     * @param  string $method
     * @param  string $url
     * @param  array $options
     * @return ResponseInterface
     */
    public function requestMeta(string $method, string $url, array $options = []): ResponseInterface;
}
