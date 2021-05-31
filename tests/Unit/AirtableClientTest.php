<?php

namespace Yoanbernabeu\AirtableClientBundle\Tests\Unit;

use DateTimeInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableRecord;
use Yoanbernabeu\AirtableClientBundle\Tests\Unit\Dummy\Customer;
use Yoanbernabeu\AirtableClientBundle\Tests\Unit\Dummy\MockResponse;

class AirtableClientTest extends TestCase
{
    private ObjectNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer =  new ObjectNormalizer();
    }

    /** @test */
    public function findAll_will_return_airtable_records(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createHttpClientMock(
            "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE",
            [
                "records" => [
                    [
                        "id" => "MOCK_ID",
                        "fields" => [
                            "firstName" => "MOCK_FIRST_NAME",
                            "lastName" => "MOCK_LAST_NAME",
                            "id" => 1,
                            "birthDay" => "1986-10-30"
                        ],
                        "createdTime" => '2021-05-20T20:05:01.000Z'
                    ]
                ]
            ]
        );

        // Given we have an airtable client
        $airtableClient = new AirtableClient(
            "MOCK_KEY",
            "MOCK_ID",
            $httpClient,
            $this->normalizer
        );

        // When we call findAll()
        $results = $airtableClient->findAll("MOCK_TABLE", null, Customer::class);

        // The result is an array of AirtableRecords
        static::assertIsArray($results);
        static::assertInstanceOf(AirtableRecord::class, $results[0]);
        static::assertContainsOnlyInstancesOf(
            Customer::class,
            array_map(fn (AirtableRecord $record) => $record->getFields(), $results)
        );

        $firstRecord = $results[0];

        static::assertEquals("MOCK_ID", $firstRecord->getId());
        static::assertEquals(new DateTimeImmutable('2021-05-20T20:05:01.000Z'), $firstRecord->getCreatedTime());

        /** @var Customer $customer */
        $customer = $firstRecord->getFields();

        static::assertEquals("MOCK_FIRST_NAME", $customer->firstName);
        static::assertEquals("MOCK_LAST_NAME", $customer->lastName);
        static::assertEquals(1, $customer->id);
        static::assertEquals("1986-10-30", $customer->birthDay);
    }

    /** @test */
    public function findAll_will_return_objects_if_data_class_is_set(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createHttpClientMock(
            "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE",
            [
                "records" => [
                    [
                        "id" => "MOCK_ID",
                        "fields" => [
                            "firstName" => "MOCK_FIRST_NAME",
                            "lastName" => "MOCK_LAST_NAME",
                            "id" => 1,
                            "birthDay" => "1986-10-30"
                        ],
                        "createdTime" => '2021-05-20T20:05:01.000Z'
                    ]
                ]
            ]
        );

        // Given we have an airtable client
        $airtableClient = new AirtableClient(
            "MOCK_KEY",
            "MOCK_ID",
            $httpClient,
            $this->normalizer
        );

        // When we call findAll with a given data class
        $results = $airtableClient->findAll("MOCK_TABLE", null, Customer::class);

        // The result is an array of AirtableRecords
        static::assertIsArray($results);
        static::assertInstanceOf(AirtableRecord::class, $results[0]);
        static::assertContainsOnlyInstancesOf(
            Customer::class,
            array_map(fn (AirtableRecord $record) => $record->getFields(), $results)
        );

        $firstRecord = $results[0];

        static::assertEquals("MOCK_ID", $firstRecord->getId());
        static::assertEquals(new DateTimeImmutable('2021-05-20T20:05:01.000Z'), $firstRecord->getCreatedTime());

        /** @var Customer $customer */
        $customer = $firstRecord->getFields();

        static::assertEquals("MOCK_FIRST_NAME", $customer->firstName);
        static::assertEquals("MOCK_LAST_NAME", $customer->lastName);
        static::assertEquals(1, $customer->id);
        static::assertEquals("1986-10-30", $customer->birthDay);
    }

    /** @test */
    public function findBy_will_return_objects_if_data_class_is_set(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createHttpClientMock(
            "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE?filterByFormula=AND({MOCK_FIELD} = 'MOCK_VALUE')",
            [
                "records" => [
                    [
                        "id" => "MOCK_ID",
                        "fields" => [
                            "firstName" => "MOCK_FIRST_NAME",
                            "lastName" => "MOCK_LAST_NAME",
                            "id" => 1,
                            "birthDay" => "1986-10-30"
                        ],
                        "createdTime" => '2021-05-20T20:05:01.000Z'
                    ]
                ]
            ]
        );

        // Given we have an airtable client
        $airtableClient = new AirtableClient(
            "MOCK_KEY",
            "MOCK_ID",
            $httpClient,
            $this->normalizer
        );

        // When we call findBy with a data class
        $results = $airtableClient->findBy("MOCK_TABLE", "MOCK_FIELD", "MOCK_VALUE", Customer::class);

        // Then the result should be an array of AirtableRecords
        $firstRecord = $results[0];
        static::assertInstanceOf(AirtableRecord::class, $firstRecord);

        // And the fields should be instance of Customer
        static::assertInstanceOf(Customer::class, $firstRecord->getFields());

        static::assertEquals("MOCK_ID", $firstRecord->getId());
        static::assertEquals(new DateTimeImmutable('2021-05-20T20:05:01.000Z'), $firstRecord->getCreatedTime());

        /** @var Customer $customer */
        $customer = $firstRecord->getFields();

        static::assertEquals("MOCK_FIRST_NAME", $customer->firstName);
        static::assertEquals("MOCK_LAST_NAME", $customer->lastName);
        static::assertEquals(1, $customer->id);
        static::assertEquals("1986-10-30", $customer->birthDay);
    }

    /** @test */
    public function findLatest_will_return_null(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createHttpClientMock(
            "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE?pageSize=1&sort%5B0%5D%5Bfield%5D=MOCK_FIELD&sort%5B0%5D%5Bdirection%5D=desc",
            [
                "records" => [
                    []
                ]
            ]
        );

        // Given we have an airtable client
        $airtableClient = new AirtableClient(
            "MOCK_KEY",
            "MOCK_ID",
            $httpClient,
            $this->normalizer
        );

        // When we call findTheLatest with a data class
        $results = $airtableClient->findTheLatest("MOCK_TABLE", "MOCK_FIELD", Customer::class);

        static::assertNull($results);
    }

    /** @test */
    public function findLatest_will_return_objects_if_data_class_is_set(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createHttpClientMock(
            "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE?pageSize=1&sort%5B0%5D%5Bfield%5D=MOCK_FIELD&sort%5B0%5D%5Bdirection%5D=desc",
            [
                "records" => [
                    [
                        "id" => "MOCK_ID",
                        "fields" => [
                            "firstName" => "MOCK_FIRST_NAME",
                            "lastName" => "MOCK_LAST_NAME",
                            "id" => 1,
                            "birthDay" => "1986-10-30"
                        ],
                        "createdTime" => '2021-05-20T20:05:01.000Z'
                    ]
                ]
            ]
        );

        // Given we have an airtable client
        $airtableClient = new AirtableClient(
            "MOCK_KEY",
            "MOCK_ID",
            $httpClient,
            $this->normalizer
        );

        // When we call findTheLatest with a data class
        $record = $airtableClient->findTheLatest("MOCK_TABLE", "MOCK_FIELD", Customer::class);

        // Then the result should be a single AirtableRecord
        static::assertInstanceOf(AirtableRecord::class, $record);
        // And the fields should be instance of Customer
        static::assertInstanceOf(Customer::class, $record->getFields());
        static::assertEquals("MOCK_ID", $record->getId());
        static::assertEquals(new DateTimeImmutable('2021-05-20T20:05:01.000Z'), $record->getCreatedTime());

        /** @var Customer $customer */
        $customer = $record->getFields();

        static::assertEquals("MOCK_FIRST_NAME", $customer->firstName);
        static::assertEquals("MOCK_LAST_NAME", $customer->lastName);
        static::assertEquals(1, $customer->id);
        static::assertEquals("1986-10-30", $customer->birthDay);
    }

    /** @test */
    public function findOneById_will_return_object_if_data_class_is_set(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createHttpClientMock(
            "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE/MOCK_ID",
            [
                "id" => "MOCK_ID",
                "fields" => [
                    "firstName" => "MOCK_FIRST_NAME",
                    "lastName" => "MOCK_LAST_NAME",
                    "id" => 1,
                    "birthDay" => "1986-10-30"
                ],
                "createdTime" => '2021-05-20T20:05:01.000Z'
            ]
        );

        // Given we have an airtable client
        $airtableClient = new AirtableClient(
            "MOCK_KEY",
            "MOCK_ID",
            $httpClient,
            $this->normalizer
        );

        // When we call findOneById with a data class
        $record = $airtableClient->findOneById("MOCK_TABLE", "MOCK_ID", Customer::class);
        static::assertEquals("MOCK_ID", $record->getId());
        static::assertEquals(new DateTimeImmutable('2021-05-20T20:05:01.000Z'), $record->getCreatedTime());

        // Then the result should be an AirtableRecord
        static::assertInstanceOf(AirtableRecord::class, $record);
        // And the fields should be instance of Customer
        static::assertInstanceOf(Customer::class, $record->getFields());

        /** @var Customer $customer */
        $customer = $record->getFields();

        static::assertEquals("MOCK_FIRST_NAME", $customer->firstName);
        static::assertEquals("MOCK_LAST_NAME", $customer->lastName);
        static::assertEquals(1, $customer->id);
        static::assertEquals("1986-10-30", $customer->birthDay);
    }

    private function createHttpClientMock(
        string $expectedCallUrl,
        array $expectedJsonData = [],
        string $expectedMethod = 'GET'
    ): HttpClientInterface {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with($expectedMethod, $expectedCallUrl)
            ->willReturn(new MockResponse(
                json_encode(
                    $expectedJsonData
                )
            ));

        return $httpClient;
    }
}
