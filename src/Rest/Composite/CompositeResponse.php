<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 5:29 PM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

use JMS\Serializer\Annotation as Serializer;

class CompositeResponse implements CompositeResponseInterface
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $success;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @var array|RequestError[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Composite\Model\RequestError>")
     */
    private $errors;

    /**
     * @var array|null
     * @Serializer\Type("array")
     */
    private $warnings;

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success === true;
    }

    /**
     * @param bool $success
     *
     * @return CompositeResponse
     */
    public function setSuccess(bool $success): CompositeResponse
    {
        $this->success = $success;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     *
     * @return CompositeResponse
     */
    public function setId(?string $id): CompositeResponse
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return RequestError[]|array
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * @param RequestError[]|array $errors
     *
     * @return CompositeResponse
     */
    public function setErrors($errors): CompositeResponse
    {
        $this->errors = $errors;

        return $this;
    }

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
     * @return CompositeResponse
     */
    public function setWarnings(?array $warnings): CompositeResponse
    {
        $this->warnings = $warnings;

        return $this;
    }
}
