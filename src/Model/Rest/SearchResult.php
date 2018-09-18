<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 11:33 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use JMS\Serializer\Annotation as Serializer;

class SearchResult
{
    /**
     * @var array|CompositeSObject[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject>")
     */
    private $searchRecords = [];

    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $metadata = [];

    /**
     * @return CompositeSObject[]|array
     */
    public function getSearchRecords()
    {
        return $this->searchRecords;
    }

    /**
     * @param CompositeSObject[]|array $searchRecords
     *
     * @return SearchResult
     */
    public function setSearchRecords($searchRecords)
    {
        $this->searchRecords = $searchRecords;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     *
     * @return SearchResult
     */
    public function setMetadata(array $metadata): SearchResult
    {
        $this->metadata = $metadata;

        return $this;
    }
}
