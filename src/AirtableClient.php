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

    public function findAll(string $table, ?string $view = null)
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

    public function findOneById(string $table, string $id)
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'https://api.airtable.com/v0/'. $this->id .'/'. $table . '/' . $id, [
            'auth_bearer' => $this->key,
        ]);

        return $response->toArray();
    }
}
