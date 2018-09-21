<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 11:10 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class SubRequestResult
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\Batch
 * @Serializer\ExclusionPolicy("none")
 */
class SubRequestResult
{
    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $statusCode = 0;

    /**
     * @var array|null
     * @Serializer\Type("array")
     */
    private $result;

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     *
     * @return SubRequestResult
     */
    public function setStatusCode(int $statusCode): SubRequestResult
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getResult(): ?array
    {
        return $this->result;
    }

    /**
     * @param array|null $result
     *
     * @return SubRequestResult
     */
    public function setResult(?array $result): SubRequestResult
    {
        $this->result = $result;

        return $this;
    }
}
