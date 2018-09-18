<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 5:30 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class RequestError
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 * @Serializer\ExclusionPolicy("NONE")
 */
class RequestError
{
    /**
     * @var array|null
     * @Serializer\Type("array")
     */
    private $fields;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $message;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $statusCode;

    /**
     * @return array|null
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }

    /**
     * @param array|null $fields
     *
     * @return RequestError
     */
    public function setFields(?array $fields): RequestError
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param null|string $message
     *
     * @return RequestError
     */
    public function setMessage(?string $message): RequestError
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getStatusCode(): ?string
    {
        return $this->statusCode;
    }

    /**
     * @param null|string $statusCode
     *
     * @return RequestError
     */
    public function setStatusCode(?string $statusCode): RequestError
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
