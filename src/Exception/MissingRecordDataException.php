<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Exception;

use Exception;

/**
 * MissingRecordDataException.
 */
final class MissingRecordDataException extends Exception
{
    public static function missingData(array $missingFields): self
    {
        return new self(sprintf('Wrong payload given, missing "%s"', implode(', ', $missingFields)));
    }

    public static function invalidCreatedTime(string $value): self
    {
        return new self(sprintf('Value passed in the "createdTime" value "%s" is not a valid DateTime', $value));
    }
}
