<?php

namespace Yoanbernabeu\AirtableClientBundle\Tests\Functionnal;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\Tests\Functionnal\Mock\Customer;
use Yoanbernabeu\AirtableClientBundle\Tests\MockResponse;

class AirtableClientTest extends TestCase
{
    /** @test */
    public function findAll_will_return_airtable_records()
    {
        // Given we have an AirtableClient
        $normalizer = new ObjectNormalizer();

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with("GET", "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE")
            ->willReturn(new MockResponse(json_encode(["records" => ["data" => "MOCK_DATA"]])));

        $airtableClient = new AirtableClient("MOCK_KEY", "MOCK_ID", $httpClient, $normalizer);

        // When we call findAll()
        $results = $airtableClient->findAll("MOCK_TABLE");

        // HttpClient should have been called
        $this->assertSame(["data" => "MOCK_DATA"], $results);
    }

    /** @test */
    public function findAll_will_return_objects_if_data_class_is_set()
    {
        // Given we have an AirtableClient
        $normalizer = new ObjectNormalizer();
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with("GET", "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE")
            ->willReturn(new MockResponse(
                json_encode(
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
                )
            ));


        $airtableClient = new AirtableClient("MOCK_KEY", "MOCK_ID", $httpClient, $normalizer);

        $results = $airtableClient->findAll("MOCK_TABLE", null, Customer::class);

        $object = $results[0]["fields"];

        $this->assertInstanceOf(Customer::class, $object);
    }

    /** @test */
    public function findBy_will_return_objects_if_data_class_is_set()
    {
        // Given we have an AirtableClient
        $normalizer = new ObjectNormalizer();
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with("GET", "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE?filterByFormula=AND({MOCK_FIELD} = 'MOCK_VALUE')")
            ->willReturn(new MockResponse(
                json_encode(
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
                )
            ));


        $airtableClient = new AirtableClient("MOCK_KEY", "MOCK_ID", $httpClient, $normalizer);

        $results = $airtableClient->findBy("MOCK_TABLE", "MOCK_FIELD", "MOCK_VALUE", Customer::class);

        $object = $results[0]["fields"];

        $this->assertInstanceOf(Customer::class, $object);
    }

    /** @test */
    public function findLatest_will_return_objects_if_data_class_is_set()
    {
        // Given we have an AirtableClient
        $normalizer = new ObjectNormalizer();
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with("GET", "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE?pageSize=1&sort%5B0%5D%5Bfield%5D=MOCK_FIELD&sort%5B0%5D%5Bdirection%5D=desc")
            ->willReturn(new MockResponse(
                json_encode(
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
                )
            ));


        $airtableClient = new AirtableClient("MOCK_KEY", "MOCK_ID", $httpClient, $normalizer);

        $results = $airtableClient->findTheLatest("MOCK_TABLE", "MOCK_FIELD", Customer::class);

        $object = $results["fields"];

        $this->assertInstanceOf(Customer::class, $object);
    }

    /** @test */
    public function findOneById_will_return_object_if_data_class_is_set()
    {
        // Given we have an AirtableClient
        $normalizer = new ObjectNormalizer();
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with("GET", "https://api.airtable.com/v0/MOCK_ID/MOCK_TABLE/MOCK_ID")
            ->willReturn(new MockResponse(
                json_encode(
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
                )
            ));


        $airtableClient = new AirtableClient("MOCK_KEY", "MOCK_ID", $httpClient, $normalizer);

        $results = $airtableClient->findOneById("MOCK_TABLE", "MOCK_ID", Customer::class);

        $object = $results["fields"];

        $this->assertInstanceOf(Customer::class, $object);
    }
}