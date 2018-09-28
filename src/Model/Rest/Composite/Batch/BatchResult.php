<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 11:02 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class BatchResult
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\Batch
 * @Serializer\ExclusionPolicy("none")
 */
class BatchResult
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $hasErrors = false;

    /**
     * @var array|SubRequestResult
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequestResult>")
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
     * @return BatchResult
     */
    public function setHasErrors(bool $hasErrors): BatchResult
    {
        $this->hasErrors = $hasErrors;

        return $this;
    }

    /**
     * @return array|SubRequestResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param array $results
     *
     * @return BatchResult
     */
    public function setResults(array $results): BatchResult
    {
        $this->results = $results;

        return $this;
    }
}
