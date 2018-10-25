<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 5:24 PM
 */

namespace AE\SalesforceRestSdk\Bulk;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class JobInfo
 *
 * @package AE\SalesforceRestSdk\Bulk
 * @Serializer\XmlRoot("jobInfo")
 * @Serializer\XmlNamespace("http://www.force.com/2009/06/asyncapi/dataload")
 * @Serializer\ExclusionPolicy("none")
 */
class JobInfo
{
    public const STATE_OPEN      = "Open";
    public const STATE_QUEUED    = "Queued";
    public const STATE_COMPLETED = "Completed";
    public const STATE_CLOSED    = "Closed";

    public const TYPE_CSV  = "CSV";
    public const TYPE_XML  = "XML";
    public const TYPE_JSON = "JSON";

    public const QUERY = "query";
    public const QUERY_ALL = "queryall";
    public const INSERT = "insert";
    public const UPDATE = "update";
    public const UPSERT = "upsert";
    public const DELETE = "delete";
    public const HARD_DELETE = "hardDelete";

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $operation;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $object;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $createdById;

    /**
     * @var \DateTimeImmutable|null
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uO'>")
     */
    private $createdDate;

    /**
     * @var \DateTimeImmutable|null
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uO'>")
     */
    private $systemModstamp;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $state;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $concurrencyMode;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $contentType;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numberBatchesQueued;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numberBatchesInProgress;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numberBatchesCompleted;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numberBatchesFailed;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numberBatchesTotal;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numRetries;

    /**
     * @var double|null
     * @Serializer\Type("double")
     */
    private $apiVersion;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numberRecordsFailed;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $totalProcessingTime;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $apiActiveProcessingTime;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $apexProcessingTime;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $externalIdFieldName;

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     *
     * @return JobInfo
     */
    public function setId(?string $id): JobInfo
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * @param null|string $operation
     *
     * @return JobInfo
     */
    public function setOperation(?string $operation): JobInfo
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * @param null|string $object
     *
     * @return JobInfo
     */
    public function setObject(?string $object): JobInfo
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCreatedById(): ?string
    {
        return $this->createdById;
    }

    /**
     * @param null|string $createdById
     *
     * @return JobInfo
     */
    public function setCreatedById(?string $createdById): JobInfo
    {
        $this->createdById = $createdById;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedDate(): ?\DateTimeImmutable
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTimeImmutable $createdDate
     *
     * @return JobInfo
     */
    public function setCreatedDate(\DateTimeImmutable $createdDate): JobInfo
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @return null|\DateTimeImmutable
     */
    public function getSystemModstamp(): ?\DateTimeImmutable
    {
        return $this->systemModstamp;
    }

    /**
     * @param null|string $systemModstamp
     *
     * @return JobInfo
     */
    public function setSystemModstamp(?string $systemModstamp): JobInfo
    {
        $this->systemModstamp = $systemModstamp;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param null|string $state
     *
     * @return JobInfo
     */
    public function setState(?string $state): JobInfo
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getConcurrencyMode(): ?string
    {
        return $this->concurrencyMode;
    }

    /**
     * @param null|string $concurrencyMode
     *
     * @return JobInfo
     */
    public function setConcurrencyMode(?string $concurrencyMode): JobInfo
    {
        $this->concurrencyMode = $concurrencyMode;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @param null|string $contentType
     *
     * @return JobInfo
     */
    public function setContentType(?string $contentType): JobInfo
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberBatchesQueued(): ?int
    {
        return $this->numberBatchesQueued;
    }

    /**
     * @param int|null $numberBatchesQueued
     *
     * @return JobInfo
     */
    public function setNumberBatchesQueued(?int $numberBatchesQueued): JobInfo
    {
        $this->numberBatchesQueued = $numberBatchesQueued;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberBatchesInProgress(): ?int
    {
        return $this->numberBatchesInProgress;
    }

    /**
     * @param int|null $numberBatchesInProgress
     *
     * @return JobInfo
     */
    public function setNumberBatchesInProgress(?int $numberBatchesInProgress): JobInfo
    {
        $this->numberBatchesInProgress = $numberBatchesInProgress;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberBatchesCompleted(): ?int
    {
        return $this->numberBatchesCompleted;
    }

    /**
     * @param int|null $numberBatchesCompleted
     *
     * @return JobInfo
     */
    public function setNumberBatchesCompleted(?int $numberBatchesCompleted): JobInfo
    {
        $this->numberBatchesCompleted = $numberBatchesCompleted;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberBatchesFailed(): ?int
    {
        return $this->numberBatchesFailed;
    }

    /**
     * @param int|null $numberBatchesFailed
     *
     * @return JobInfo
     */
    public function setNumberBatchesFailed(?int $numberBatchesFailed): JobInfo
    {
        $this->numberBatchesFailed = $numberBatchesFailed;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberBatchesTotal(): ?int
    {
        return $this->numberBatchesTotal;
    }

    /**
     * @param int|null $numberBatchesTotal
     *
     * @return JobInfo
     */
    public function setNumberBatchesTotal(?int $numberBatchesTotal): JobInfo
    {
        $this->numberBatchesTotal = $numberBatchesTotal;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumRetries(): ?int
    {
        return $this->numRetries;
    }

    /**
     * @param int|null $numRetries
     *
     * @return JobInfo
     */
    public function setNumRetries(?int $numRetries): JobInfo
    {
        $this->numRetries = $numRetries;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getApiVersion(): ?float
    {
        return $this->apiVersion;
    }

    /**
     * @param float|null $apiVersion
     *
     * @return JobInfo
     */
    public function setApiVersion(?float $apiVersion): JobInfo
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberRecordsFailed(): ?int
    {
        return $this->numberRecordsFailed;
    }

    /**
     * @param int|null $numberRecordsFailed
     *
     * @return JobInfo
     */
    public function setNumberRecordsFailed(?int $numberRecordsFailed): JobInfo
    {
        $this->numberRecordsFailed = $numberRecordsFailed;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalProcessingTime(): ?int
    {
        return $this->totalProcessingTime;
    }

    /**
     * @param int|null $totalProcessingTime
     *
     * @return JobInfo
     */
    public function setTotalProcessingTime(?int $totalProcessingTime): JobInfo
    {
        $this->totalProcessingTime = $totalProcessingTime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getApiActiveProcessingTime(): ?int
    {
        return $this->apiActiveProcessingTime;
    }

    /**
     * @param int|null $apiActiveProcessingTime
     *
     * @return JobInfo
     */
    public function setApiActiveProcessingTime(?int $apiActiveProcessingTime): JobInfo
    {
        $this->apiActiveProcessingTime = $apiActiveProcessingTime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getApexProcessingTime(): ?int
    {
        return $this->apexProcessingTime;
    }

    /**
     * @param int|null $apexProcessingTime
     *
     * @return JobInfo
     */
    public function setApexProcessingTime(?int $apexProcessingTime): JobInfo
    {
        $this->apexProcessingTime = $apexProcessingTime;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExternalIdFieldName(): ?string
    {
        return $this->externalIdFieldName;
    }

    /**
     * @param null|string $externalIdFieldName
     *
     * @return JobInfo
     */
    public function setExternalIdFieldName(?string $externalIdFieldName): JobInfo
    {
        $this->externalIdFieldName = $externalIdFieldName;

        return $this;
    }
}
