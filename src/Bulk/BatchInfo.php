<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 5:42 PM
 */

namespace AE\SalesforceRestSdk\Bulk;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class BatchInfo
 *
 * @package AE\SalesforceRestSdk\Bulk
 * @Serializer\XmlRoot("BatchInfo")
 * @Serializer\XmlNamespace("http://www.force.com/2009/06/asyncapi/dataload")
 */
class BatchInfo
{
    public const STATE_OPEN          = "Open";
    public const STATE_NOT_PROCESSED = "Not Processed";
    public const STATE_PROCESSING    = "Processing";
    public const STATE_QUEUED        = "Queued";
    public const STATE_COMPLETED     = "Completed";
    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $jobId;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $state;

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
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numberRecordsProcessed;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $numberRecordsFinished;

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
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     *
     * @return BatchInfo
     */
    public function setId(?string $id): BatchInfo
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getJobId(): ?string
    {
        return $this->jobId;
    }

    /**
     * @param null|string $jobId
     *
     * @return BatchInfo
     */
    public function setJobId(?string $jobId): BatchInfo
    {
        $this->jobId = $jobId;

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
     * @return BatchInfo
     */
    public function setState(?string $state): BatchInfo
    {
        $this->state = $state;

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
     * @param \DateTimeImmutable|null $createdDate
     *
     * @return BatchInfo
     */
    public function setCreatedDate(?\DateTimeImmutable $createdDate): BatchInfo
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getSystemModstamp(): ?\DateTimeImmutable
    {
        return $this->systemModstamp;
    }

    /**
     * @param \DateTimeImmutable|null $systemModstamp
     *
     * @return BatchInfo
     */
    public function setSystemModstamp(?\DateTimeImmutable $systemModstamp): BatchInfo
    {
        $this->systemModstamp = $systemModstamp;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberRecordsProcessed(): ?int
    {
        return $this->numberRecordsProcessed;
    }

    /**
     * @param int|null $numberRecordsProcessed
     *
     * @return BatchInfo
     */
    public function setNumberRecordsProcessed(?int $numberRecordsProcessed): BatchInfo
    {
        $this->numberRecordsProcessed = $numberRecordsProcessed;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberRecordsFinished(): ?int
    {
        return $this->numberRecordsFinished;
    }

    /**
     * @param int|null $numberRecordsFinished
     *
     * @return BatchInfo
     */
    public function setNumberRecordsFinished(?int $numberRecordsFinished): BatchInfo
    {
        $this->numberRecordsFinished = $numberRecordsFinished;

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
     * @return BatchInfo
     */
    public function setTotalProcessingTime(?int $totalProcessingTime): BatchInfo
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
     * @return BatchInfo
     */
    public function setApiActiveProcessingTime(?int $apiActiveProcessingTime): BatchInfo
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
     * @return BatchInfo
     */
    public function setApexProcessingTime(?int $apexProcessingTime): BatchInfo
    {
        $this->apexProcessingTime = $apexProcessingTime;

        return $this;
    }
}
