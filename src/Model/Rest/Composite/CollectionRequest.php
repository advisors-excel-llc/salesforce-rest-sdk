<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 5:05 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use AE\SalesforceRestSdk\Model\SObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class CollectionRequest
 *
 * @package AE\SalesforceRestSdk\Composite\Model
 * @Serializer\ExclusionPolicy("none")
 */
class CollectionRequest implements CollectionRequestInterface
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $allOrNone = false;

    /**
     * @var Collection
     * @Serializer\Type("ArrayCollection<AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject>")
     */
    private $records;

    public function __construct(array $records = [], bool $allOrNone = false)
    {
        $this->allOrNone = $allOrNone;
        $this->records   = new ArrayCollection($records);
    }

    /**
     * @return bool
     */
    public function isAllOrNone(): bool
    {
        return $this->allOrNone;
    }

    /**
     * @param bool $allOrNone
     *
     * @return CollectionRequest
     */
    public function setAllOrNone(bool $allOrNone): CollectionRequest
    {
        $this->allOrNone = $allOrNone;

        return $this;
    }

    public function getRecords(): Collection
    {
        return $this->records;
    }

    /**
     * @param $records
     *
     * @return CollectionRequest
     */
    public function setRecords($records): CollectionRequest
    {
        if ($records instanceof Collection) {
            $this->records = $records;
        } elseif (is_array($records)) {
            $this->records = new ArrayCollection($records);
        } elseif ($records instanceof SObject) {
            $this->records = new ArrayCollection([$records]);
        } else {
            throw new \InvalidArgumentException(
                '$records must be a value of Collection, array of SObjects or an SObject.'
            );
        }

        return $this;
    }

    /**
     * @param SObject $record
     *
     * @return CollectionRequest
     */
    public function addRecord(SObject $record): CollectionRequest
    {
        if (!$this->records->contains($record)) {
            $this->records->add($record);
        }

        return $this;
    }

    /**
     * @param SObject $record
     *
     * @return CollectionRequest
     */
    public function removeRecord(SObject $record): CollectionRequest
    {
        if ($this->records->contains($record)) {
            $this->records->remove($record);
        }

        return $this;
    }

    /**
     * @param array $records
     * @param bool $allOrNone
     *
     * @return CollectionRequest
     */
    public static function create(array $records = [], bool $allOrNone = false)
    {
        return new static($records, $allOrNone);
    }
}
