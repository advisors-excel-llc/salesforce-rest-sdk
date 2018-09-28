<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/20/18
 * Time: 12:49 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class CompositeCollection
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite
 * @Serializer\ExclusionPolicy("none")
 */
class CompositeCollection
{
    /**
     * @var array|CompositeSObject[]
     * @Serializer\Type("array< AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject>")
     */
    private $records = [];

    public function __construct(array $records = [])
    {
        $this->records = $records;
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
     * @return CompositeCollection
     */
    public function setRecords($records)
    {
        $this->records = $records;

        return $this;
    }
}
