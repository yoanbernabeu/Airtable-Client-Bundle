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
    public function createFromRecordWillThrowIfADataIsMissingInArray(array $recordData)
    {
        $this->expectException(MissingRecordDataException::class);

        $airtableRecord = AirtableRecord::createFromRecord($recordData);
    }

    public function invalidRecordArrayProvider()
    {
        yield [[
            'id' => 'MOCK_ID',
            'createdTime' => '2021-01-01',
        ]];
        yield [[
            'fields' => [],
            'createdTime' => '2021-01-01',
        ]];
        yield [[
            'id' => 'MOCK_ID',
            'createdTime' => '2021-01-01',
        ]];
        yield [[
            'id' => 'MOCK_ID',
            'fields' => [],
            'createdTime' => 'not a valid datetime',
        ]];
        yield [[]];
    }
}
