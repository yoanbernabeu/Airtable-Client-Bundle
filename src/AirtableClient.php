<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * AirtableClient.
 */
class AirtableClient implements AirtableClientInterface
{
    private string $key;
    private string $id;
    private HttpClientInterface $httpClient;
    private ObjectNormalizer $normalizer;

    public function __construct(
        string $key,
        string $id,
        HttpClientInterface $httpClient,
        ObjectNormalizer $objectNormalizer
    ) {
        $this->key = $key;
        $this->id = $id;
        $this->httpClient = $httpClient;
        $this->normalizer = $objectNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(string $table, ?string $view = null, ?string $dataClass = null): array
    {
        $url = sprintf(
            '%s/%s%s',
            $this->id,
            $table,
            null !== $view ? '?view='.$view : ''
        );

        $response = $this->request('GET', $url);

        return $this->mapRecordsToAirtableRecords($response->toArray()['records'], $dataClass);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(string $table, string $field, string $value, ?string $dataClass = null): array
    {
        $filterByFormula = sprintf("?filterByFormula=AND({%s} = '%s')", $field, $value);
        $url = sprintf('%s/%s%s', $this->id, $table, $filterByFormula);
        $response = $this->request('GET', $url);

        return $this->mapRecordsToAirtableRecords($response->toArray()['records'], $dataClass);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneById(string $table, string $id, ?string $dataClass = null): ?AirtableRecord
    {
        $url = sprintf('%s/%s/%s', $this->id, $table, $id);
        $response = $this->request('GET', $url);

        $recordData = $response->toArray();

        $recordData = $this->createRecordFromResponse($dataClass, $recordData);

        return AirtableRecord::createFromRecord($recordData);
    }

    /**
     * {@inheritdoc}
     */
    public function findTheLatest(string $table, $field, ?string $dataClass = null): ?AirtableRecord
    {
        $params = [
            'pageSize' => 1,
            'sort' => [
                0 => [
                    'field' => $field,
                    'direction' => 'desc',
                ],
            ],
        ];
        $url = sprintf(
            '%s/%s?%s',
            $this->id,
            $table,
            http_build_query($params)
        );
        $response = $this->request('GET', $url);

        $recordData = $response->toArray()['records'][0];

        if (!$recordData) {
            return null;
        }

        $recordData = $this->createRecordFromResponse($dataClass, $recordData);

        return AirtableRecord::createFromRecord($recordData);
    }

    /**
     * {@inheritdoc}
     */
    public function addOneRecord(string $table, array $fields, ?string $dataClass = null): ?AirtableRecord
    {
        $url = sprintf(
            '%s/%s',
            $this->id,
            $table
        );
        $response = $this->request('POST', $url, $fields);

        $recordData = $response->toArray();

        if (null === $recordData) {
            return null;
        }

        $recordData = $this->createRecordFromResponse($dataClass, $recordData);

        return AirtableRecord::createFromRecord($recordData);
    }

    /**
     * Use the HttpClient to request Airtable API and returns the response.
     */
    private function request(string $method, string $url, ?array $body = null): ResponseInterface
    {
        $params = ['auth_bearer' => $this->key];

        if ('POST' === $method) {
            $params = $params + ['headers' => ['Content-Type' => 'application/json']];
            $params = $params + ['json' => ['fields' => $body]];
        }

        return $this->httpClient->request(
            $method,
            'https://api.airtable.com/v0/'.$url,
            $params
        );
    }

    /**
     * Turns an array of arrays to an array of AirtableRecord objects.
     *
     * @param array  $records   An array of arrays
     * @param string $dataClass Optionnal class name which will hold record's fields
     *
     * @return array An array of AirtableRecords objects
     */
    private function mapRecordsToAirtableRecords(array $records, string $dataClass = null): array
    {
        return array_map(
            function (array $recordData) use ($dataClass): AirtableRecord {
                if (null !== $dataClass) {
                    $recordData = $this->createRecordFromResponse($dataClass, $recordData);
                }

                return AirtableRecord::createFromRecord($recordData);
            },
            $records
        );
    }

    /**
     * Create record from response.
     *
     * @return array An AirtableRecord object
     */
    private function createRecordFromResponse(?string $dataClass = null, array $recordData)
    {
        if (null !== $dataClass) {
            $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);

            return $recordData;
        }

        return $recordData;
    }
}
