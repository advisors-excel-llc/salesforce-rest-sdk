<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 10:57 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Metadata;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use JMS\Serializer\Annotation as Serializer;

class BasicInfo
{
    /**
     * @var DescribeSObject
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Metadata\DescribeSObject")
     */
    private $objectDescribe;

    /**
     * @var array|CompositeSObject[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject>")
     */
    private $recentItems = [];

    /**
     * @return DescribeSObject
     */
    public function getObjectDescribe(): DescribeSObject
    {
        return $this->objectDescribe;
    }

    /**
     * @param DescribeSObject $objectDescribe
     *
     * @return BasicInfo
     */
    public function setObjectDescribe(DescribeSObject $objectDescribe): BasicInfo
    {
        $this->objectDescribe = $objectDescribe;

        return $this;
    }

    /**
     * @return CompositeSObject[]|array
     */
    public function getRecentItems()
    {
        return $this->recentItems;
    }

    /**
     * @param CompositeSObject[]|array $recentItems
     *
     * @return BasicInfo
     */
    public function setRecentItems($recentItems)
    {
        $this->recentItems = $recentItems;

        return $this;
    }
}
