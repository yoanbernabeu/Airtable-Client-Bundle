<?php

namespace Yoanbernabeu\AirtableClientBundle;

use DateTime;
use DateTimeInterface;

class AirtableRecord
{
    protected $fields;
    protected string $id;
    protected DateTimeInterface $createdTime;

    public function __construct(string $id, $fields, DateTimeInterface $createdTime)
    {
        $this->fields = $fields;
        $this->id = $id;
        $this->createdTime = $createdTime;
    }

    public static function fromRecord(array $record): self
    {
        return new self($record['id'], $record['fields'], new DateTime($record['createdTime']));
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;

        return $this;
    }
}
