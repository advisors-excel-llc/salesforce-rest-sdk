<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 3:52 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class SubRequest
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\Batch
 * @Serializer\ExclusionPolicy("none")
 */
abstract class SubRequest
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var mixed
     */
    protected $body;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    protected $referenceId;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    protected $url;

    /**
     * @var ArrayCollection
     * @Serializer\Type("ArrayCollection<string, string>")
     */
    protected $httpHeaders;

    public function __construct(string $method, ?string $referenceId = null)
    {
        if (null === $referenceId) {
            $referenceId = uniqid("ref_");
        }

        $this->method      = $method;
        $this->referenceId = $referenceId;
        $this->httpHeaders = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return SubRequest
     */
    public function setMethod(string $method): SubRequest
    {
        $this->method = $method;

        return $this;
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
     * @return SubRequest
     */
    public function setBody($body): SubRequest
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    /**
     * @param string $referenceId
     *
     * @return SubRequest
     */
    public function setReferenceId(string $referenceId): SubRequest
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return SubRequest
     */
    public function setUrl(string $url): SubRequest
    {
        $this->url = $url;

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
     * @return SubRequest
     */
    public function setHttpHeaders(ArrayCollection $httpHeaders): SubRequest
    {
        $this->httpHeaders = $httpHeaders;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return SubRequest
     */
    public function addHeader(string $name, string $value): SubRequest
    {
        $this->httpHeaders->set($name, $value);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return SubRequest
     */
    public function removeHeader(string $name): SubRequest
    {
        if ($this->httpHeaders->containsKey($name)) {
            $this->httpHeaders->remove($name);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getResultClass(): ?string
    {
        return null;
    }
}
