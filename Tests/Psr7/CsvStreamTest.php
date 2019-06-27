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
        $stream = stream_for($testLine);
        $csv = new CsvStream($stream);

        $line = CsvStream::readline($stream);

        $this->assertEquals(rtrim($testLine, "\n"), $line);

        $stream->rewind();

        $row = $csv->read();

        $this->assertEquals(
            [
                'Item 1',
                'Item 2',
                'This is a longer'.PHP_EOL.'multiline field',
                'Last Field'
            ],
            $row
        );
    }

    public function testGetContents()
    {
        $testLine = '"Name","Alt Name","Description","Alt Desc"'.PHP_EOL.
            '"Item 1","Item 2","This is a longer'.PHP_EOL.'multiline field","Last Field"'.PHP_EOL;
        $stream = stream_for($testLine);
        $csv = new CsvStream($stream);

        foreach ($csv->getContents(true) as $row) {
            if (false === $row) {
                break;
            }
        }

        $this->assertEquals(
            [
                "Name" => 'Item 1',
                "Alt Name" => 'Item 2',
                "Description" => 'This is a longer'.PHP_EOL.'multiline field',
                "Alt Desc" => 'Last Field',
            ],
            $row
        );
    }
}
