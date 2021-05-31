<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Unit\Dummy;

use Symfony\Contracts\HttpClient\ResponseInterface;

class DummyResponse implements ResponseInterface
{
    public string $content;
    public int $status;
    public array $headers;

    public function __construct(string $content = '', int $status = 200, array $headers = [])
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
        return json_decode($this->content, true, 512, JSON_THROW_ON_ERROR);
    }
}
