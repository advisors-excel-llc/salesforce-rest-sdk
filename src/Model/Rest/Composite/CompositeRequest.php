<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 11:17 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class CompositeRequest
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite
 * @Serializer\ExclusionPolicy("none")
 */
class CompositeRequest
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $allOrNone = false;

    /**
     * @var SubRequest[]|array
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest>")
     */
    private $compositeRequest = [];

    /**
     * @return bool
     */
    public function isAllOrNone(): bool
    {
        return $this->allOrNone;
    }

    /**
     * @param bool $allOrNone
     *
     * @return CompositeRequest
     */
    public function setAllOrNone(bool $allOrNone): CompositeRequest
    {
        $this->allOrNone = $allOrNone;

        return $this;
    }

    /**
     * @return SubRequest[]|array
     */
    public function getCompositeRequest()
    {
        return $this->compositeRequest;
    }

    /**
     * @param SubRequest[]|array $compositeRequest
     *
     * @return CompositeRequest
     */
    public function setCompositeRequest($compositeRequest)
    {
        $this->compositeRequest = $compositeRequest;

        return $this;
    }

    /**
     * @param string $referenceId
     *
     * @return SubRequest|null
     */
    public function findSubRequestByReferenceId(string $referenceId): ?SubRequest
    {
        foreach ($this->compositeRequest as $subRequest) {
            if ($subRequest->getReferenceId() === $referenceId) {
                return $subRequest;
            }
        }

        return null;
    }
}
