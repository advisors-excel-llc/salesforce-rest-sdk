<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 3:52 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch;

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
    protected $richInput;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    protected $url;

    public function __construct(string $method)
    {
        $this->method = $method;
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
    public function getRichInput()
    {
        return $this->richInput;
    }

    /**
     * @param $richInput
     *
     * @return SubRequest
     */
    public function setRichInput($richInput): SubRequest
    {
        $this->richInput = $richInput;

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
}
