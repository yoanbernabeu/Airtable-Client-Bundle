<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AirtableClient implements AirtableClientInterface
{
    private AirtableTransportInterface $airtableTransport;
    private NormalizerInterface $normalizer;

    public function __construct(
        AirtableTransportInterface $airtableTransport,
        NormalizerInterface $normalizer
    ) {
        $this->airtableTransport = $airtableTransport;
        $this->normalizer = $normalizer;
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

        $response = $this->pagination($url, $this->airtableTransport->request('GET', $url)->toArray());

        return $this->mapRecordsToAirtableRecords($response['records'], $dataClass);
    }

    /**
     * {@inheritdoc}
     */
    public function pagination(string $url, array $response): array
    {
        if ($response['offset'] ?? null) {
            $param = mb_stristr($url, '?view') ? '&' : '?';

            $offsetUrl = $url.$param.'offset='.$response['offset'];
            $offsetResponse = $this->airtableTransport->request('GET', $offsetUrl)->toArray();
            $response = array_merge($response['records'], $offsetResponse['records']);

            return $this->pagination($url, ['records' => $response, 'offset' => $offsetResponse['offset'] ?? null]);
        }

        return $response;
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
    public function findByDateField(string $table, string $field, string $value, ?string $dataClass = null): array
    {
        $filterByFormula = sprintf("?filterByFormula=AND(DATESTR({%s}) = '%s')", $field, $value);
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
        $url = sprintf('%s', $table);

        return $this->createOrUpdateRecord('POST', $url, $fields, $dataClass);
    }

    /**
     * {@inheritdoc}
     */
    public function update(string $table, string $recordId, array $fields, ?string $dataClass = null): ?AirtableRecord
    {
        $url = sprintf('%s/%s', $table, $recordId);

        return $this->createOrUpdateRecord('PATCH', $url, $fields, $dataClass);
    }

    /**
     * {@inheritdoc}
     */
    public function getTablesMetadata(): ?array
    {
        $response = $this->airtableTransport->requestMeta('GET', 'tables');

        return $response->toArray()['tables'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableMetadata(string $table): ?array
    {
        $tables = $this->getTablesMetadata() ?? [];
        foreach ($tables as $value) {
            if ($value['name'] === $table) {
                return $value;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function createTable(
        string $name,
        array $fields = null,
        string $description = null,
    ): array {
        $json = [
            'name' => $name,
            'fields' => $fields,
        ];
        if (!is_null($description)) {
            $json['description'] = $description;
        }
        $response = $this->airtableTransport->requestMeta('POST', 'tables', ['json' => $json]);

        return $response->toArray();
    }

    /**
     * Create a new field in a table.
     * https://airtable.com/developers/web/api/create-field
     *
     * @param string $table
     * @param string $name
     * @param string|null $type https://airtable.com/developers/web/api/model/field-type
     * @param string|null $description
     * @param mixed[]|null $options https://airtable.com/developers/web/api/field-model
     *
     * @return mixed[] Field model with name https://airtable.com/developers/web/api/field-model
     */
    public function createField(
        string $table,
        string $name,
        string $type = null,
        string $description = null,
        array $options = null
    ): array {
        $url = sprintf('tables/%s/fields', $table);
        $json = [
            'name' => $name,
        ];
        if (!is_null($type)) {
            $json['type'] = $type;
        }
        if (!is_null($description)) {
            $json['description'] = $description;
        }
        if (!is_null($options)) {
            $json['options'] = $options;
        }
        $response = $this->airtableTransport->requestMeta('POST', $url, ['json' => $json]);

        return $response->toArray();
    }

    public function createForm(array $fields): FormInterface
    {
        $form = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory()
            ->createBuilder()
        ;

        foreach ($fields as $fieldName => $fieldType) {
            $form->add($fieldName, $fieldType);
        }

        return $form->getForm();
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
    private function createRecordFromResponse(?string $dataClass, array $recordData)
    {
        if (null !== $dataClass) {
            $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);

            return $recordData;
        }

        return $recordData;
    }

    /**
     * @throws Exception\MissingRecordDataException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function createOrUpdateRecord(string $method, string $url, array $fields, ?string $dataClass): ?AirtableRecord
    {
        $response = $this->airtableTransport->request(
            $method,
            $url,
            [
                'json' => ['fields' => $fields],
            ]
        );

        $recordData = $response->toArray();

        if ([] === $recordData) {
            return null;
        }

        $recordData = $this->createRecordFromResponse($dataClass, $recordData);

        return AirtableRecord::createFromRecord($recordData);
    }
}
