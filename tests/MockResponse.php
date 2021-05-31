<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests;

use Symfony\Contracts\HttpClient\ResponseInterface;

class MockResponse implements ResponseInterface
{
    public $content;
    public $status;
    public $headers;

    public function __construct($content = '', $status = 200, $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function getContent(bool $throw = true): string
    {
        return '';
    }

    public function getHeaders(bool $throw = true): array
    {
        return [];
    }

    public function getInfo(?string $type = null)
    {
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    public function cancel(): void
    {
    }

    public function toArray(bool $throw = true): array
    {
        return json_decode($this->content, true);
    }
}
