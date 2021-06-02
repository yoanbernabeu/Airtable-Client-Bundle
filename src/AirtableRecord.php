<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Yoanbernabeu\AirtableClientBundle\Exception\MissingRecordDataException;

final class AirtableRecord
{
    private const MANDATORY_FIELDS = [
        'id',
        'fields',
        'createdTime',
    ];

    /**
     * @var object|array<array-key, mixed>
     */
    private $fields;
    private string $id;
    private DateTimeInterface $createdTime;

    /**
     * @param object|array<array-key, mixed> $fields
     */
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
     * - createdTime : the record created time (should be a valid datetime value).
     *
     * @param array $record The airtable record
     *
     * @throws MissingRecordDataException
     * @throws Exception
     */
    public static function createFromRecord(array $record): self
    {
        self::assertRecordPayload($record);

        ['id' => $id, 'fields' => $fields, 'createdTime' => $createdTime] = $record;

        return new self(
            $id,
            $fields,
            new DateTimeImmutable($createdTime)
        );
    }

    /**
     * @return object|array<array-key, mixed>
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedTime(): DateTimeInterface
    {
        return $this->createdTime;
    }

    /**
     * Assert that the record payload and can be transformed to a AirtableRecord object.
     *
     * @throws MissingRecordDataException
     */
    private static function assertRecordPayload(array $payload): void
    {
        if ([] !== $missingFields = array_diff_key(array_flip(self::MANDATORY_FIELDS), $payload)) {
            throw MissingRecordDataException::missingData(array_keys($missingFields));
        }

        try {
            new DateTimeImmutable($payload['createdTime'] ?? '');
        } catch (Exception $e) {
            throw MissingRecordDataException::invalidCreatedTime($payload['createdTime']);
        }
    }
}
