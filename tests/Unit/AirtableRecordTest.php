<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Yoanbernabeu\AirtableClientBundle\AirtableRecord;
use Yoanbernabeu\AirtableClientBundle\Exception\MissingRecordDataException;

/**
 * @internal
 */
class AirtableRecordTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidRecordArrayProvider
     */
    public function createFromRecordWillThrowIfADataIsMissingInArray(array $recordData, string $message)
    {
        $this->expectException(MissingRecordDataException::class);
        $this->expectErrorMessage($message);

        AirtableRecord::createFromRecord($recordData);
    }

    public function invalidRecordArrayProvider()
    {
        yield 'Missing fields' => [[
            'id' => 'MOCK_ID',
            'createdTime' => '2021-01-01',
        ], 'Wrong payload given, missing "fields"'];
        yield 'Missing Id' => [[
            'fields' => [],
            'createdTime' => '2021-01-01',
        ], 'Wrong payload given, missing "id"'];
        yield 'Invalid Datetime' => [[
            'id' => 'MOCK_ID',
            'fields' => [],
            'createdTime' => 'not a valid datetime',
        ], 'Value passed in the "createdTime" value "not a valid datetime" is not a valid DateTime'];
        yield 'Empty payload' => [[], 'Wrong payload given, missing "id, fields, createdTime"'];
    }
}
