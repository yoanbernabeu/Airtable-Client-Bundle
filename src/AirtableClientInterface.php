<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

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
     * Returns a set of rows from AirTable.
     *
     * @param string $url      Url to get data
     * @param array  $response Array response from Airtable
     */
    public function pagination(string $url, array $response): array;

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
    public function find(string $table, string $id, ?string $dataClass = null): ?AirtableRecord;

    /**
     * Field allowing filtering.
     *
     * @param string      $table     Table name
     * @param mixed       $field
     * @param string|null $dataClass The name of the class which will hold fields data
     */
    public function findLast(string $table, $field, ?string $dataClass = null): ?AirtableRecord;

    /**
     * Create new record and return the new record of a table.
     *
     * @param string      $table     Table name
     * @param array       $fields    Table fields
     * @param string|null $dataClass The name of the class which will hold fields data
     */
    public function add(string $table, array $fields, ?string $dataClass = null): ?AirtableRecord;

    /**
     * Create new record and return the new record of a table.
     *
     * @param string      $table     Table name
     * @param array       $fields    Table fields
     * @param string|null $dataClass The name of the class which will hold fields data
     */
    public function update(string $table, string $recordId, array $fields, ?string $dataClass = null): ?AirtableRecord;

    /**
     * Create form from an array of fields.
     *
     * @param array $fields Fields of Form
     */
    public function createForm(array $fields): FormInterface;

    /**
     * Returns the schema of the tables in the specified base in Array.
     */
    public function getTablesMetadata(): ?array;

    /**
     * Returns the schema of one table (by name) in the specified base in Array.
     */
    public function getTableMetadata(string $table): ?array;
}
