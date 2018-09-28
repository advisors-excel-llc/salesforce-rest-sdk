<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 9:49 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class SubRequestResult
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite
 * @Serializer\ExclusionPolicy("none")
 */
class SubRequestResult
{
    /**
     * @var mixed
     * @Serializer\Type("array")
     */
    private $body;

    /**
     * @var ArrayCollection
     * @Serializer\Type("ArrayCollection<string, string>")
     */
    private $httpHeaders;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $httpStatusCode;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $referenceId;

    public function __construct()
    {
        $this->httpHeaders = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param $body
     *
     * @return SubRequestResult
     */
    public function setBody($body): SubRequestResult
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getHttpHeaders(): ArrayCollection
    {
        return $this->httpHeaders;
    }

    /**
     * @param ArrayCollection $httpHeaders
     *
     * @return SubRequestResult
     */
    public function setHttpHeaders(ArrayCollection $httpHeaders): SubRequestResult
    {
        $this->httpHeaders = $httpHeaders;

        return $this;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * @param int $httpStatusCode
     *
     * @return SubRequestResult
     */
    public function setHttpStatusCode(int $httpStatusCode): SubRequestResult
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceId(): string
    {
        return $this->referenceId;
    }

    /**
     * @param string $referenceId
     *
     * @return SubRequestResult
     */
    public function setReferenceId(string $referenceId): SubRequestResult
    {
        $this->referenceId = $referenceId;

        return $this;
    }
}
