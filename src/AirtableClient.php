<?php

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\HttpClient\HttpClient;
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

        $airtableRecords = $response->toArray()['records'];

        if ($dataClass) {
            $records = [];

            foreach ($airtableRecords as $record) {
                $data = [
                    "id" => $record['id'],
                    "fields" => $this->normalizer->denormalize($record['fields'], $dataClass),
                    "createdTime" => $record['createdTime']
                ];

                $records[] = $data;
            }

            return $records;
        }

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

        $airtableRecords = $response->toArray()['records'];

        if ($dataClass) {
            $records = [];

            foreach ($airtableRecords as $record) {
                $data = [
                    "id" => $record['id'],
                    "fields" => $this->normalizer->denormalize($record['fields'], $dataClass),
                    "createdTime" => $record['createdTime']
                ];

                $records[] = $data;
            }

            return $records;
        }

        return $airtableRecords;
    }

    /**
     * findOneById
     *
     * @param  mixed $table Table Name
     * @param  mixed $id Id
     * @param  string $dataClass The name of the class which will hold fields data 
     * @return array
     */
    public function findOneById(string $table, string $id, ?string $dataClass = null): array
    {
        $url = $this->id . '/' . $table . '/' . $id;
        $response = $this->request($url);

        $data = $response->toArray();

        if ($dataClass) {
            return [
                'id' => $data['id'],
                'fields' => $this->normalizer->denormalize($data['fields'], $dataClass),
                'createdTime' => $data['createdTime']
            ];
        }

        return $data;
    }

    /**
     * findTheLatest
     *
     * Field allowing filtering
     *
     * @param  mixed $table Table name
     * @param  mixed $field
     * @param  string $dataClass The name of the class which will hold fields data
     * @return array
     */
    public function findTheLatest(string $table, $field, ?string $dataClass = null): array
    {
        $url = $this->id . '/'
            . $table . '?pageSize=1&sort%5B0%5D%5Bfield%5D='
            . $field . '&sort%5B0%5D%5Bdirection%5D=desc';
        $response = $this->request($url);

        $data = $response->toArray()['records'][0];

        if ($dataClass) {
            return [
                'id' => $data['id'],
                'fields' => $this->normalizer->denormalize($data['fields'], $dataClass),
                'createdTime' => $data['createdTime']
            ];
        }

        return $data;
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
