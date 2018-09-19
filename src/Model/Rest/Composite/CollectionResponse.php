<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 5:29 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use AE\SalesforceRestSdk\Model\Rest\CreateResponse;
use JMS\Serializer\Annotation as Serializer;

class CollectionResponse extends CreateResponse
{
    /**
     * @var array|null
     * @Serializer\Type("array")
     */
    private $warnings;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $message;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $errorCode;

    /**
     * @var array
     * @Serializer\Type("array<string>")
     */
    private $fields = [];

    /**
     * @return array|null
     */
    public function getWarnings(): ?array
    {
        return $this->warnings;
    }

    /**
     * @param array|null $warnings
     *
     * @return CollectionResponse
     */
    public function setWarnings(?array $warnings): CollectionResponse
    {
        $this->warnings = $warnings;

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
     * @return CollectionResponse
     */
    public function setMessage(?string $message): CollectionResponse
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * @param null|string $errorCode
     *
     * @return CollectionResponse
     */
    public function setErrorCode(?string $errorCode): CollectionResponse
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return CollectionResponse
     */
    public function setFields(array $fields): CollectionResponse
    {
        $this->fields = $fields;

        return $this;
    }
}
