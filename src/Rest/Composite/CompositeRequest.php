<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 5:05 PM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

use AE\SalesforceRestSdk\Model\SObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class CompositeRequest
 *
 * @package AE\SalesforceRestSdk\Composite\Model
 * @Serializer\ExclusionPolicy("none")
 */
class CompositeRequest implements CompositeRequestInterface
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $allOrNone = false;

    /**
     * @var Collection
     * @Serializer\Type("ArrayCollection<AE\SalesforceRestSdk\Model\SObject>")
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
     * @return CompositeRequest
     */
    public function setAllOrNone(bool $allOrNone): CompositeRequest
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
     * @return CompositeRequest
     */
    public function setRecords($records): CompositeRequest
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
     * @return CompositeRequest
     */
    public function addRecord(SObject $record): CompositeRequest
    {
        if (!$this->records->contains($record)) {
            $this->records->add($record);
        }

        return $this;
    }

    /**
     * @param SObject $record
     *
     * @return CompositeRequest
     */
    public function removeRecord(SObject $record): CompositeRequest
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
     * @return CompositeRequest
     */
    public static function create(array $records = [], bool $allOrNone = false)
    {
        return new static($records, $allOrNone);
    }
}
