<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

interface AirtableClientInterface
{
    /**
     * Returns a set of rows from AirTable.
     *
     * @param string      $table     Table name
     * @param string|null $view      View name
     * @param string|null $dataClass The class name which will hold fields data
     *
     * @return array<array-key, AirtableRecord>
     */
    public function findAll(string $table, ?string $view = null, ?string $dataClass = null): array;

    /**
     * Allows you to filter on a field in the table.
     *
     * @param string      $table     Table name
     * @param string      $field     Search field name
     * @param string      $value     Wanted value
     * @param string|null $dataClass The class name which will hold fields data
     *
     * @return array<array-key, AirtableRecord>
     */
    public function findBy(string $table, string $field, string $value, ?string $dataClass = null): array;

    /**
     * Returns one record of a table by its ID.
     *
     * @param string      $table     Table Name
     * @param string      $id        Id
     * @param string|null $dataClass The name of the class which will hold fields data
     */
    public function findOneById(string $table, string $id, ?string $dataClass = null): ?AirtableRecord;

    /**
     * Field allowing filtering.
     *
     * @param string      $table     Table name
     * @param mixed       $field
     * @param string|null $dataClass The name of the class which will hold fields data
     */
    public function findTheLatest(string $table, $field, ?string $dataClass = null): ?AirtableRecord;

    /**
     * Create news records and return the new record of a table.
     *
     * @param string      $table     Table name
     * @param array       $fields    Table fields
     * @param string|null $dataClass The name of the class which will hold fields data
     */
    public function addOneRecord(string $table, array $fields, ?string $dataClass = null): ?AirtableRecord;
}
