<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 10:41 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class CreateResponse
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 * @Serializer\ExclusionPolicy("none")
 */
class CreateResponse
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    protected $success;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    protected $id;

    /**
     * @var array|RequestError[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\RequestError>")
     */
    protected $errors;

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     *
     * @return CreateResponse
     */
    public function setSuccess(bool $success)
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
     * @return CreateResponse
     */
    public function setId(?string $id)
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
     * @return CreateResponse
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }
}
