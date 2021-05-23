<?php

namespace Yoanbernabeu\AirtableClientBundle\Services;

class JsonToArray
{
    public function convert(string $json)
    {
        return json_decode($json, true);
    }
}
