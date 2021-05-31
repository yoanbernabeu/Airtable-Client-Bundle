<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

interface AirtableClientInterface
{
    /**
     * Returns a set of rows from AirTable
     *
     * @param  mixed   $table Table name
     * @param  mixed   $view  View name
     * @param  ?string  $dataClass The class name which will hold fields data
     * @return array<array-key, AirtableRecord>
     */
    public function findAll(string $table, ?string $view = null, ?string $dataClass = null): array;

    /**
     * Allows you to filter on a field in the table
     *
     * @param  mixed $table Table name
     * @param  mixed $field Search field name
     * @param  mixed $value Wanted value
     * @param  ?string $dataClass The class name which will hold fields data
     * @return array<array-key, AirtableRecord>
     */
    public function findBy(string $table, string $field, string $value, ?string $dataClass = null): array;


    /**
     * Returns one record of a table by its ID
     *
     * @param  mixed $table Table Name
     * @param  mixed $id Id
     * @param  ?string $dataClass The name of the class which will hold fields data
     * @return ?AirtableRecord
     */
    public function findOneById(string $table, string $id, ?string $dataClass = null): ?AirtableRecord;

    /**
     * Field allowing filtering
     *
     * @param  mixed $table Table name
     * @param  mixed $field
     * @param  ?string $dataClass The name of the class which will hold fields data
     * @return AirtableRecord|null
     */
    public function findTheLatest(string $table, $field, ?string $dataClass = null): ?AirtableRecord;
}
