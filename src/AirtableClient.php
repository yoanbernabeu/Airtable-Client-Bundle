<?php

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\HttpClient\HttpClient;
use Yoanbernabeu\AirtableClientBundle\Services\JsonToArray;

class AirtableClient
{
    public function __construct($key, $id)
    {
        $this->key = $key;
        $this->id = $id;
    }

    public function getData(string $table, ?string $view = null)
    {
        $client = HttpClient::create();
        $convert = new JsonToArray();

        if ($view) {
            $view = '?view=' . $view;
        }

        $response = $client->request('GET', 'https://api.airtable.com/v0/'. $this->id .'/'. $table . $view, [
            'auth_bearer' => $this->key,
        ]);

        return $convert->convert($response->getContent())['records'];
    }
}
