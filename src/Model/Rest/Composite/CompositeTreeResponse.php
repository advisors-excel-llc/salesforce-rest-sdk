<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/20/18
 * Time: 1:00 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class CompositeTreeResponse
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite
 * @Serializer\ExclusionPolicy("none")
 */
class CompositeTreeResponse
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $hasErrors = false;

    /**
     * @var array|CollectionResponse[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Composite\CollectionResponse>")
     */
    private $results = [];

    /**
     * @return bool
     */
    public function isHasErrors(): bool
    {
        return $this->hasErrors;
    }

    /**
     * @param bool $hasErrors
     *
     * @return CompositeTreeResponse
     */
    public function setHasErrors(bool $hasErrors): CompositeTreeResponse
    {
        $this->hasErrors = $hasErrors;

        return $this;
    }

    /**
     * @return CollectionResponse[]|array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param CollectionResponse|array $results
     *
     * @return CompositeTreeResponse
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }
}
