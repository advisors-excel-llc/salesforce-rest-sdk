<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 10/31/18
 * Time: 10:12 AM
 */

namespace AE\SalesforceRestSdk\Tests\Psr7;

use AE\SalesforceRestSdk\Psr7\CsvStream;
use function GuzzleHttp\Psr7\stream_for;
use PHPUnit\Framework\TestCase;

class CsvStreamTest extends TestCase
{
    public function testRead()
    {
        $testLine = '"Item 1","Item 2","This is a longer'.PHP_EOL.'multiline field","Last Field"'.PHP_EOL;
        $stream   = stream_for($testLine);
        $csv      = new CsvStream($stream);

        $line = CsvStream::readline($stream);

        $this->assertEquals(rtrim($testLine, "\n"), $line);

        $stream->rewind();
        $rows = [];

        while ($row = $csv->read()) {
            $rows[] = $row;
        }

        $this->assertEquals(
            [
                [
                    'Item 1',
                    'Item 2',
                    'This is a longer'.PHP_EOL.'multiline field',
                    'Last Field',
                ],
            ],
            $rows
        );
    }

    public function testGetContents()
    {
        $testLine = '"Name","Alt Name","Description","Alt Desc"'.PHP_EOL.
            '"Item 1","Item 2","This is a longer'.PHP_EOL.'multiline field","Last Field"'.PHP_EOL.
            '"Item 1.5","Item 2.2","This is a longer'.PHP_EOL.'multiline field","Last Field"'.PHP_EOL;
        $stream   = stream_for($testLine);
        $csv      = new CsvStream($stream);

        $rows = [];
        foreach ($csv->getContents(true) as $row) {
            $rows[] = $row;
        }

        $this->assertEquals(
            [
                [
                    "Name"        => 'Item 1',
                    "Alt Name"    => 'Item 2',
                    "Description" => 'This is a longer'.PHP_EOL.'multiline field',
                    "Alt Desc"    => 'Last Field',
                ],
                [
                    "Name"        => 'Item 1.5',
                    "Alt Name"    => 'Item 2.2',
                    "Description" => 'This is a longer'.PHP_EOL.'multiline field',
                    "Alt Desc"    => 'Last Field',
                ],
            ],
            $rows
        );
    }

    public function testWrite()
    {
        $resource = fopen("php://memory", 'r+');
        $stream   = stream_for($resource);
        $csv      = new CsvStream($stream);

        $csv->write(
            [
                'Name',
                'Alt Name',
                'Description',
                'Alt Desc',
            ]
        );

        $csv->write(
            [
                'Test 1',
                'Test Alt 1',
                'Test Description'.PHP_EOL.' thingy',
                'Other description',
            ]
        );

        $csv->write(
            [
                'Test 2',
                'Test Alt 2',
                'Test Description'.PHP_EOL.' thingy 2',
                'Other description 2',
            ]
        );

        $csv->rewind();

        $rows = [];

        foreach ($csv->getContents(true) as $row) {
            $rows[] = $row;
        }

        $this->assertEquals(
            [
                [
                    'Name'        => 'Test 1',
                    'Alt Name'    => 'Test Alt 1',
                    'Description' => 'Test Description'.PHP_EOL.' thingy',
                    'Alt Desc'    => 'Other description',
                ],
                [
                    'Name'        => 'Test 2',
                    'Alt Name'    => 'Test Alt 2',
                    'Description' => 'Test Description'.PHP_EOL.' thingy 2',
                    'Alt Desc'    => 'Other description 2',
                ],
            ],
            $rows
        );
    }
}
