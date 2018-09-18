<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 11:12 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use JMS\Serializer\Annotation as Serializer;

class QueryResult
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $done = false;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $totalSize = 0;

    /**
     * @var array|CompositeSObject[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject>")
     */
    private $records = [];

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $nextRecordsUrl;

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * @param bool $done
     *
     * @return QueryResult
     */
    public function setDone(bool $done): QueryResult
    {
        $this->done = $done;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalSize(): int
    {
        return $this->totalSize;
    }

    /**
     * @param int $totalSize
     *
     * @return QueryResult
     */
    public function setTotalSize(int $totalSize): QueryResult
    {
        $this->totalSize = $totalSize;

        return $this;
    }

    /**
     * @return CompositeSObject[]|array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param CompositeSObject[]|array $records
     *
     * @return QueryResult
     */
    public function setRecords($records)
    {
        $this->records = $records;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNextRecordsUrl(): ?string
    {
        return $this->nextRecordsUrl;
    }

    /**
     * @param null|string $nextRecordsUrl
     *
     * @return QueryResult
     */
    public function setNextRecordsUrl(?string $nextRecordsUrl): QueryResult
    {
        $this->nextRecordsUrl = $nextRecordsUrl;

        return $this;
    }
}
