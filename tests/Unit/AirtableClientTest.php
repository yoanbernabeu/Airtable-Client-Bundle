<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Yoanbernabeu\AirtableClientBundle\AirtableClient;
use Yoanbernabeu\AirtableClientBundle\AirtableRecord;
use Yoanbernabeu\AirtableClientBundle\AirtableTransportInterface;
use Yoanbernabeu\AirtableClientBundle\Tests\Unit\Dummy\Customer;

/**
 * @internal
 */
class AirtableClientTest extends TestCase
{
    private ObjectNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new ObjectNormalizer();
    }

    /** @test */
    public function findAllWillReturnAirtableRecords(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createAirtableTransportMock(
            'MOCK_TABLE',
            $this->getRecordPayload()
        );

        // Given we have an airtable client
        $airtableClient = new AirtableClient(
            $httpClient,
            $this->normalizer
        );

        // When we call findAll()
        $results = $airtableClient->findAll('MOCK_TABLE', null, Customer::class);

        // The result is an array of AirtableRecords
        static::assertIsArray($results);
        static::assertInstanceOf(AirtableRecord::class, $results[0]);
        static::assertContainsOnlyInstancesOf(
            Customer::class,
            array_map(fn (AirtableRecord $record) => $record->getFields(), $results)
        );

        $firstRecord = $results[0];

        $this->assertRecordIdandCreatedTime($firstRecord);

        /** @var Customer $customer */
        $customer = $firstRecord->getFields();

        $this->assertCustomer($customer);
    }

    /** @test */
    public function findAllWillReturnObjectsIfDataClassIsSet(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createAirtableTransportMock(
            'MOCK_TABLE',
            $this->getRecordPayload()
        );

        // Given we have an airtable client
        $airtableClient = new AirtableClient(
            $httpClient,
            $this->normalizer
        );

        // When we call findAll with a given data class
        $results = $airtableClient->findAll('MOCK_TABLE', null, Customer::class);

        // The result is an array of AirtableRecords
        static::assertIsArray($results);
        static::assertInstanceOf(AirtableRecord::class, $results[0]);
        static::assertContainsOnlyInstancesOf(
            Customer::class,
            array_map(fn (AirtableRecord $record) => $record->getFields(), $results)
        );

        $firstRecord = $results[0];

        $this->assertRecordIdandCreatedTime($firstRecord);

        /** @var Customer $customer */
        $customer = $firstRecord->getFields();

        $this->assertCustomer($customer);
    }

    /** @test */
    public function findByWillReturnObjectsIfDataClassIsSet(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createAirtableTransportMock(
            "MOCK_TABLE?filterByFormula=AND({MOCK_FIELD} = 'MOCK_VALUE')",
            $this->getRecordPayload()
        );

        // Given we have an airtable client
        $airtableClient = $this->getAirtableClient($httpClient);

        // When we call findBy with a data class
        $results = $airtableClient->findBy('MOCK_TABLE', 'MOCK_FIELD', 'MOCK_VALUE', Customer::class);

        // Then the result should be an array of AirtableRecords
        $firstRecord = $results[0];
        static::assertInstanceOf(AirtableRecord::class, $firstRecord);

        // And the fields should be instance of Customer
        static::assertInstanceOf(Customer::class, $firstRecord->getFields());

        $this->assertRecordIdandCreatedTime($firstRecord);

        /** @var Customer $customer */
        $customer = $firstRecord->getFields();

        $this->assertCustomer($customer);
    }

    /** @test */
    public function findLatestWillReturnNull(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createAirtableTransportMock(
            'MOCK_TABLE?pageSize=1&sort%5B0%5D%5Bfield%5D=MOCK_FIELD&sort%5B0%5D%5Bdirection%5D=desc',
            [
                'records' => [
                    [],
                ],
            ]
        );

        // Given we have an airtable client
        $airtableClient = $this->getAirtableClient($httpClient);

        // When we call findTheLatest with a data class
        $results = $airtableClient->findTheLatest('MOCK_TABLE', 'MOCK_FIELD', Customer::class);

        static::assertNull($results);
    }

    /** @test */
    public function findLatestWillReturnObjectsIfDataClassIsSet(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createAirtableTransportMock(
            'MOCK_TABLE?pageSize=1&sort%5B0%5D%5Bfield%5D=MOCK_FIELD&sort%5B0%5D%5Bdirection%5D=desc',
            $this->getRecordPayload()
        );

        // Given we have an airtable client
        $airtableClient = $this->getAirtableClient($httpClient);

        // When we call findTheLatest with a data class
        $record = $airtableClient->findTheLatest('MOCK_TABLE', 'MOCK_FIELD', Customer::class);

        // Then the result should be a single AirtableRecord
        static::assertInstanceOf(AirtableRecord::class, $record);
        // And the fields should be instance of Customer
        static::assertInstanceOf(Customer::class, $record->getFields());
        $this->assertRecordIdandCreatedTime($record);

        /** @var Customer $customer */
        $customer = $record->getFields();

        $this->assertCustomer($customer);
    }

    /** @test */
    public function findOneByIdWillReturnObjectIfDataClassIsSet(): void
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createAirtableTransportMock(
            'MOCK_TABLE/MOCK_ID',
            $this->getPayload()
        );

        // Given we have an airtable client
        $airtableClient = $this->getAirtableClient($httpClient);

        // When we call findOneById with a data class
        $record = $airtableClient->findOneById('MOCK_TABLE', 'MOCK_ID', Customer::class);

        $this->assertRecordIdandCreatedTime($record);
        // Then the result should be an AirtableRecord
        static::assertInstanceOf(AirtableRecord::class, $record);
        // And the fields should be instance of Customer
        static::assertInstanceOf(Customer::class, $record->getFields());

        /** @var Customer $customer */
        $customer = $record->getFields();

        $this->assertCustomer($customer);
    }

    /** @test */
    public function addOneRecordWillReturnNull()
    {
        // Setup the dummy HttpClient
        $httpClient = $this->createAirtableTransportMock(
            'MOCK_TABLE',
            [],
            'POST'
        );

        // Given we have an airtable client
        $airtableClient = $this->getAirtableClient($httpClient);

        // When we call addOneRecord with a data class
        $record = $airtableClient->addOneRecord('MOCK_TABLE', [], Customer::class);

        static::assertNull($record);
    }

    /** @test */
    public function addOneRecordWillReturnObjectIfDataClassIsSet()
    {
        // Setup the dummy AirtableTransport
        $httpClient = $this->createAirtableTransportMock(
            'MOCK_TABLE',
            $this->getPayload(),
            'POST'
        );

        // Given we have an airtable client
        $airtableClient = $this->getAirtableClient($httpClient);

        // When we call addOneRecord with a data class
        $record = $airtableClient->addOneRecord('MOCK_TABLE', ['id' => 'MOCK_ID']);
        $this->assertRecordIdandCreatedTime($record);
        // Then the result should be an AirtableRecord
        static::assertInstanceOf(AirtableRecord::class, $record);
    }

    private function assertCustomer(Customer $customer): void
    {
        static::assertEquals('MOCK_FIRST_NAME', $customer->firstName);
        static::assertEquals('MOCK_LAST_NAME', $customer->lastName);
        static::assertEquals(1, $customer->id);
        static::assertEquals('1986-10-30', $customer->birthDay);
    }

    private function assertRecordIdandCreatedTime(AirtableRecord $record): void
    {
        static::assertEquals('MOCK_ID', $record->getId());
        static::assertEquals(new DateTimeImmutable('2021-05-20T20:05:01.000Z'), $record->getCreatedTime());
    }

    private function createAirtableTransportMock(
        string $expectedCallUrl,
        array $expectedData = [],
        string $expectedMethod = 'GET'
    ): AirtableTransportInterface {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('toArray')
            ->willReturn($expectedData)
        ;

        $airtableTransport = $this->createMock(AirtableTransportInterface::class);
        $airtableTransport
            ->expects(static::once())
            ->method('request')
            ->with($expectedMethod, $expectedCallUrl)
            ->willReturn($response)
        ;

        return $airtableTransport;
    }

    private function getAirtableClient(AirtableTransportInterface $airtableTransport): AirtableClient
    {
        return new AirtableClient(
            $airtableTransport,
            $this->normalizer
        );
    }

    private function getRecordPayload(): array
    {
        return [
            'records' => [$this->getPayload()],
        ];
    }

    private function getPayload(): array
    {
        return [
            'id' => 'MOCK_ID',
            'fields' => [
                'firstName' => 'MOCK_FIRST_NAME',
                'lastName' => 'MOCK_LAST_NAME',
                'id' => 1,
                'birthDay' => '1986-10-30',
            ],
            'createdTime' => '2021-05-20T20:05:01.000Z',
        ];
    }
}
