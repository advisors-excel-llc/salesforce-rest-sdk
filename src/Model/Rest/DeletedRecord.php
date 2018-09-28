<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/20/18
 * Time: 11:31 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class DeletedRecord
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite
 * @Serializer\ExclusionPolicy("none")
 */
class DeletedRecord
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uO', 'UTC'>")
     */
    private $deletedDate;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return DeletedRecord
     */
    public function setId(string $id): DeletedRecord
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDeletedDate(): \DateTimeImmutable
    {
        return $this->deletedDate;
    }

    /**
     * @param \DateTimeImmutable $deletedDate
     *
     * @return DeletedRecord
     */
    public function setDeletedDate(\DateTimeImmutable $deletedDate): DeletedRecord
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }
}
