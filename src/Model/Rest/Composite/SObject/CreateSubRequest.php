<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:54 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\PostSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\ReferenceableInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class CreateSubRequest extends PostSubRequest implements ReferenceableInterface, SObjectSubRequestInterface
{
    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectType;

    public function __construct(string $sObjectType, ?string $referenceId = null)
    {
        parent::__construct($referenceId);

        $this->sObjectType = $sObjectType;
    }

    final public function setBody($body): SubRequest
    {
        if ($body instanceof SObject) {
            $body->Id   = null;
            $body->Type = null;

            parent::setBody($body);
        }

        return $this;
    }

    /**
     * @param string $url
     *
     * @return SubRequest
     */
    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        if (null === $this->sObjectType) {
            throw new \RuntimeException("No SObjectType has been set.");
        }

        $this->url = '/'.Client::BASE_PATH.'sobjects/'.$this->sObjectType.'/';
    }

    public function reference(string $fieldName): ?string
    {
        if (ucwords($fieldName) === "Id") {
            return "@{{$this->referenceId}.Id}";
        }

        return null;
    }

    public function getSObjectType()
    {
        return $this->sObjectType;
    }
}
