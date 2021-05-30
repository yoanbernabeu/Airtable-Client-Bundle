<?php

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\HttpClient\HttpClient;

/**
 * AirtableClient
 */
class AirtableClient
{
    private string $airTableApiKey;
    private string $airTableId;
    
    public function __construct(string $airTableApiKey, string $airTableId)
    {
        $this->airTableApiKey = $airTableApiKey;
        $this->airTableId = $airTableId;
    }
    
    /**
     * findAll
     *
     * @param  mixed $table Table name
     * @param  mixed $view  View name
     * @return array
     */
    public function findAll(string $table, ?string $view = null): array
    {
        if ($view) {
            $view = '?view=' . $view;
        }

        $url = $this->airTableId .'/'. $table . $view;
        $response = $this->request($url);

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
        $filterByFormula = "?filterByFormula=AND({".$field."} = '".$value."')";
        $url = $this->airTableId .'/'. $table . $filterByFormula;
        $response = $this->request($url);

        return $response->toArray()['records'];
    }
    
    /**
     * findOneById
     *
     * @param  mixed $table Table Name
     * @param  mixed $id Id
     * @return array
     */
    public function findOneById(string $table, string $id): array
    {
        $url = $this->airTableId .'/'. $table . '/' . $id;
        $response = $this->request($url);

        return $response->toArray();
    }
    
    /**
     * findTheLatest
     *
     * Field allowing filtering
     *
     * @param  mixed $table Table name
     * @param  mixed $field
     * @return array
     */
    public function findTheLatest(string $table, $field): array
    {
        $url = $this->airTableId .'/'
            . $table . '?pageSize=1&sort%5B0%5D%5Bfield%5D='
            . $field . '&sort%5B0%5D%5Bdirection%5D=desc';
        $response = $this->request($url);

        return $response->toArray()['records'][0];
    }

    public function request(string $url)
    {
        $client = HttpClient::create();

        return $client->request(
            'GET',
            'https://api.airtable.com/v0/'. $url,
            [
                'auth_bearer' => $this->airTableApiKey,
            ]
        );
    }
}
