<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 10:31 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class BatchRequest
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\Batch
 * @Serializer\ExclusionPolicy("none")
 */
class BatchRequest
{
    /**
     * @var array|SubRequest[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest>")
     */
    private $batchRequests;

    /**
     * BatchRequest constructor.
     *
     * @param array|SubRequest[] $requests
     */
    public function __construct(array $requests)
    {
        $this->batchRequests = $requests;
    }

    /**
     * @return SubRequest[]|array
     */
    public function getBatchRequests()
    {
        return $this->batchRequests;
    }

    /**
     * @param SubRequest[]|array $batchRequests
     *
     * @return BatchRequest
     */
    public function setBatchRequests($batchRequests)
    {
        $this->batchRequests = $batchRequests;

        return $this;
    }
}
