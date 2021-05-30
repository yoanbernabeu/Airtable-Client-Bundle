<?php

namespace Yoanbernabeu\AirtableClientBundle;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Yoanbernabeu\AirtableClientBundle\Exception\MissingRecordDataException;

final class AirtableRecord
{
    private $fields;
    private string $id;
    private DateTimeInterface $createdTime;

    private function __construct(string $id, $fields, DateTimeInterface $createdTime)
    {
        $this->fields = $fields;
        $this->id = $id;
        $this->createdTime = $createdTime;
    }


    /**
     * Returns an instance of AirtableRecord from values set in an array
     * Mandatory values are :
     * - id : the record id
     * - fields : the record data fields
     * - createdTime : the record created time (should be a valid datetime value)
     *
     * @param array $record The airtable record
     *
     * @return self
     */
    public static function createFromRecord(array $record): self
    {
        static::ensureRecordValidation($record);

        return new self(
            $record['id'],
            $record['fields'],
            new DateTimeImmutable($record['createdTime'])
        );
    }

    /**
     * Allow anyone to ensure that a record array is valid and can be transformed to a AirtableRecord object
     *
     * @param array $record
     *
     * @throws MissingRecordDataException
     * 
     * @return void
     */
    public static function ensureRecordValidation(array $record): void
    {
        $neededFields = ['id', 'fields', 'createdTime'];
        $missingFields = [];

        foreach ($neededFields as $key) {
            if (!isset($record[$key])) {
                $missingFields[] = $key;
            }
        }

        if (count($missingFields) > 0) {
            throw new MissingRecordDataException(
                sprintf(
                    'Expected values missing in record array : %s',
                    implode(', ', $missingFields)
                )
            );
        }

        try {
            new DateTimeImmutable($record['createdTime']);
        } catch (Exception $e) {
            throw new MissingRecordDataException(
                sprintf(
                    'Value passed in the "createdTime" value is not a valid DateTime : %s',
                    $record['createdTime']
                )
            );
        }
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedTime(): DateTimeInterface
    {
        return $this->createdTime;
    }
}
