<?php

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\HttpClient\HttpClient;

class AirtableClient
{
    private $key;
    private $id;
    
    public function __construct($key, $id)
    {
        $this->key = $key;
        $this->id = $id;
    }

    public function findAll(string $table, ?string $view = null): array
    {
        $client = HttpClient::create();

        if ($view) {
            $view = '?view=' . $view;
        }

        $response = $client->request('GET', 'https://api.airtable.com/v0/'. $this->id .'/'. $table . $view, [
            'auth_bearer' => $this->key,
        ]);

        return $response->toArray()['records'];
    }

    
    /**
     * findBy
     *
     * Allows you to filter on a field in the table
     *
     * @param  mixed $table Table name
     * @param  mixed $field Search field name
     * @param  mixed $value Wanted value
     * @return array
     */
    public function findBy(string $table, string $field, string $value): array
    {
        $client = HttpClient::create();

        $filterByFormula = "?filterByFormula=AND({".$field."} = '".$value."')";

        $response = $client->request('GET', 'https://api.airtable.com/v0/'. $this->id .'/'. $table . $filterByFormula, [
            'auth_bearer' => $this->key,
        ]);

        return $response->toArray()['records'];
    }

    public function findOneById(string $table, string $id): array
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'https://api.airtable.com/v0/'. $this->id .'/'. $table . '/' . $id, [
            'auth_bearer' => $this->key,
        ]);

        return $response->toArray();
    }

    public function findTheLatest(string $table, $field): array
    {
        $client = HttpClient::create();

        $response = $client->request(
            'GET',
            'https://api.airtable.com/v0/'. $this->id .'/'
            . $table . '?pageSize=1&sort%5B0%5D%5Bfield%5D='
            . $field . '&sort%5B0%5D%5Bdirection%5D=desc',
            [
                'auth_bearer' => $this->key,
            ]
        );

        return $response->toArray()['records'][0];
    }
}
