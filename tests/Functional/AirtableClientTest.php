<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;

class AirtableClientTest extends KernelTestCase
{
    public function test(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has(AirtableClient::class));
    }
}
