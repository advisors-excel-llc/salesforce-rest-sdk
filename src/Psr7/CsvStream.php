<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 10/26/18
 * Time: 11:18 AM
 */

namespace AE\SalesforceRestSdk\Psr7;

use Psr\Http\Message\StreamInterface;

class CsvStream implements StreamInterface
{
    private $stream;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public function __toString()
    {
        return $this->stream->__toString();
    }

    public function close()
    {
        $this->stream->close();
    }

    public function detach()
    {
        return $this->stream->detach();
    }

    public function getSize()
    {
        return $this->stream->getSize();
    }

    public function tell()
    {
        return $this->stream->tell();
    }

    public function eof()
    {
        return $this->stream->eof();
    }

    public function isSeekable()
    {
        return $this->stream->isSeekable();
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return $this->stream->seek($offset, $whence);
    }

    public function rewind()
    {
        $this->stream->rewind();
    }

    public function isWritable()
    {
        return $this->stream->isWritable();
    }

    public function write($string)
    {
        return $this->stream->write($string);
    }

    public function isReadable()
    {
        return $this->stream->isReadable();
    }

    public function read($length = 0, string $delimiter = ",", string $enclosure = '"', string $escape = "\\")
    {
        if ($length < 0) {
            throw new \RuntimeException("The length cannot be a negative number,");
        }

        $line = \GuzzleHttp\Psr7\readline($this->stream, $length > 0 ? $length : null);

        if (false == $line) {
            return false;
        }

        return str_getcsv($line, $delimiter, $enclosure, $escape);
    }

    public function getContents(
        bool $hasHeaders = true,
        string $delimiter = ",",
        string $enclosure = '"',
        string $escape = "\\"
    ): array {
        $rows = [];

        while (false !== ($row = $this->read(0, $delimiter, $enclosure, $escape))) {
            $rows[] = $row;
        }

        if ($hasHeaders) {
            array_walk(
                $rows,
                function (&$a) use ($rows) {
                    $a = array_combine($rows[0], $a);
                }
            );
            array_shift($rows); # remove column header
        }

        return $rows;
    }

    public function getMetadata($key = null)
    {
        return $this->stream->getMetadata($key);
    }
}
