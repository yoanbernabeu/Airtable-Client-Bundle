<?php

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * AirtableClient
 */
class AirtableClient
{
    private $key;
    private $id;
    private $httpClient;
    private $normalizer;

    public function __construct($key, $id, HttpClientInterface $httpClient, ObjectNormalizer $objectNormalizer)
    {
        $this->key = $key;
        $this->id = $id;
        $this->httpClient = $httpClient;
        $this->normalizer = $objectNormalizer;
    }

    /**
     * Returns a set of rows from AirTable
     *
     * @param  mixed   $table Table name
     * @param  mixed   $view  View name
     * @param  string  $dataClass The class name which will hold fields data
     * @return array
     */
    public function findAll(string $table, ?string $view = null, ?string $dataClass = null): array
    {
        if ($view) {
            $view = '?view=' . $view;
        }

        $url = $this->id . '/' . $table . $view;
        $response = $this->request($url);

        $airtableRecords = array_map(
            function (array $recordData) use ($dataClass) {
                if ($dataClass) {
                    $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);
                }

                return AirtableRecord::createFromRecord($recordData);
            },
            $response->toArray()['records']
        );

        return $airtableRecords;
    }

    /**
     * findBy
     *
     * Allows you to filter on a field in the table
     *
     * @param  mixed $table Table name
     * @param  mixed $field Search field name
     * @param  mixed $value Wanted value
     * @param  string $dataClass The class name which will hold fields data
     * @return array
     */
    public function findBy(string $table, string $field, string $value, ?string $dataClass = null): array
    {
        $filterByFormula = "?filterByFormula=AND({" . $field . "} = '" . $value . "')";
        $url = $this->id . '/' . $table . $filterByFormula;
        $response = $this->request($url);

        $airtableRecords = array_map(
            function (array $recordData) use ($dataClass) {
                if ($dataClass) {
                    $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);
                }

                return AirtableRecord::createFromRecord($recordData);
            },
            $response->toArray()['records']
        );

        return $airtableRecords;
    }

    /**
     * findOneById
     *
     * @param  mixed $table Table Name
     * @param  mixed $id Id
     * @param  string $dataClass The name of the class which will hold fields data 
     * @return array|object
     */
    public function findOneById(string $table, string $id, ?string $dataClass = null)
    {
        $url = $this->id . '/' . $table . '/' . $id;
        $response = $this->request($url);

        $recordData = $response->toArray();

        if ($dataClass) {
            $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);
        }

        return AirtableRecord::createFromRecord($recordData);
    }

    /**
     * findTheLatest
     *
     * Field allowing filtering
     *
     * @param  mixed $table Table name
     * @param  mixed $field
     * @param  string $dataClass The name of the class which will hold fields data
     * @return AirtableRecord|null
     */
    public function findTheLatest(string $table, $field, ?string $dataClass = null): ?AirtableRecord
    {
        $url = $this->id . '/'
            . $table . '?pageSize=1&sort%5B0%5D%5Bfield%5D='
            . $field . '&sort%5B0%5D%5Bdirection%5D=desc';
        $response = $this->request($url);

        $recordData = $response->toArray()['records'][0] ?? null;

        if (!$recordData) {
            return null;
        }

        if ($dataClass) {
            $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);
        }

        $airtableRecord = AirtableRecord::createFromRecord($recordData);

        return $airtableRecord;
    }

    public function request(string $url)
    {
        $response = $this->httpClient->request(
            'GET',
            'https://api.airtable.com/v0/' . $url,
            [
                'auth_bearer' => $this->key,
            ]
        );

        return $response;
    }
}
