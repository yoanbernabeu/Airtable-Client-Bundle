<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class AirtableClient implements AirtableClientInterface
{
    private AirtableTransportInterface $airtableTransport;
    private ObjectNormalizer $normalizer;

    public function __construct(
        AirtableTransportInterface $airtableTransport,
        ObjectNormalizer $objectNormalizer
    ) {
        $this->airtableTransport = $airtableTransport;
        $this->normalizer = $objectNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(string $table, ?string $view = null, ?string $dataClass = null): array
    {
        $url = sprintf(
            '%s%s',
            $table,
            null !== $view ? '?view='.$view : ''
        );

        $response = $this->airtableTransport->request('GET', $url);

        return $this->mapRecordsToAirtableRecords($response->toArray()['records'], $dataClass);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(string $table, string $field, string $value, ?string $dataClass = null): array
    {
        $filterByFormula = sprintf("?filterByFormula=AND({%s} = '%s')", $field, $value);
        $url = sprintf('%s%s', $table, $filterByFormula);
        $response = $this->airtableTransport->request('GET', $url);

        return $this->mapRecordsToAirtableRecords($response->toArray()['records'], $dataClass);
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $table, string $id, ?string $dataClass = null): ?AirtableRecord
    {
        $url = sprintf('%s/%s', $table, $id);
        $response = $this->airtableTransport->request('GET', $url);

        $recordData = $response->toArray();

        $recordData = $this->createRecordFromResponse($dataClass, $recordData);

        return AirtableRecord::createFromRecord($recordData);
    }

    /**
     * {@inheritdoc}
     */
    public function findLast(string $table, $field, ?string $dataClass = null): ?AirtableRecord
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
            '%s?%s',
            $table,
            http_build_query($params)
        );
        $response = $this->airtableTransport->request('GET', $url);

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
    public function add(string $table, array $fields, ?string $dataClass = null): ?AirtableRecord
    {
        $url = sprintf(
            '%s',
            $table
        );

        $response = $this->airtableTransport->request(
            'POST',
            $url,
            ['json' => [
                'fields' => $fields, ],
            ]
        );

        $recordData = $response->toArray();

        if ([] === $recordData) {
            return null;
        }

        $recordData = $this->createRecordFromResponse($dataClass, $recordData);

        return AirtableRecord::createFromRecord($recordData);
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
