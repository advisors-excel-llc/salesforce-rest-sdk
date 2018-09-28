<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 10:22 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use AE\SalesforceRestSdk\Model\SObject;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class DeletedResponse
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 * @Serializer\ExclusionPolicy("none")
 */
class DeletedResponse
{
    /**
     * @var array|SObject[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\DeletedRecord>")
     */
    private $deletedRecords = [];

    /**
     * @var \DateTimeImmutable|null
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uO'>")
     */
    private $earliestDateAvailable;

    /**
     * @var \DateTimeImmutable|null
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uO'>")
     */
    private $latestDateCovered;

    /**
     * @return SObject[]|array
     */
    public function getDeletedRecords()
    {
        return $this->deletedRecords;
    }

    /**
     * @param SObject[]|array $deletedRecords
     *
     * @return DeletedResponse
     */
    public function setDeletedRecords($deletedRecords)
    {
        $this->deletedRecords = $deletedRecords;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEarliestDateAvailable(): ?\DateTime
    {
        return $this->earliestDateAvailable;
    }

    /**
     * @param \DateTimeImmutable|null $earliestDateAvailable
     *
     * @return DeletedResponse
     */
    public function setEarliestDateAvailable(?\DateTimeImmutable $earliestDateAvailable): DeletedResponse
    {
        $this->earliestDateAvailable = $earliestDateAvailable;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLatestDateCovered(): ?\DateTimeImmutable
    {
        return $this->latestDateCovered;
    }

    /**
     * @param \DateTime|null $latestDateCovered
     *
     * @return DeletedResponse
     */
    public function setLatestDateCovered(?\DateTime $latestDateCovered): DeletedResponse
    {
        $this->latestDateCovered = $latestDateCovered;

        return $this;
    }
}
