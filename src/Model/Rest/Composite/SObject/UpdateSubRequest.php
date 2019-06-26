<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:36 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\PatchSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class UpdateSubRequest extends PatchSubRequest implements SObjectSubRequestInterface
{
    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectType;

    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectId;

    /**
     * UpdateSubRequest constructor.
     *
     * @param string $version
     * @param string $sObjectType
     * @param null|string|SObject $sObject
     * @param null|string $referenceId
     */
    public function __construct(
        string $sObjectType,
        $sObject = null,
        string $version = "44.0",
        ?string $referenceId = null
    ) {
        parent::__construct($version, $referenceId);

        $this->sObjectType = $sObjectType;

        if (null !== $sObject && is_string($sObject)) {
            $this->sObjectId = $sObject;
        } elseif (null !== $sObject && $sObject instanceof SObject) {
            $this->setBody($sObject);
        }
    }

    final public function setBody($body): SubRequest
    {
        if ($body instanceof SObject) {
            parent::setBody($body);

            if (null !== $body->Id) {
                $this->sObjectId = $body->Id;
            }
        }

        return $this;
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        if (null === $this->sObjectType || null === $this->sObjectId || null === $this->getBody()) {
            throw new \RuntimeException("The UpdateSubRequest is incomplete.");
        }

        $this->url      = $this->getBasePath().$this->sObjectType.'/'.$this->sObjectId;
        $this->body->Id = null;
    }

    /**
     * @Serializer\PostDeserialize()
     */
    public function postSerialize()
    {
        $this->body->Id = $this->sObjectId;
    }

    /**
     * @return string
     */
    public function getSObjectType(): string
    {
        return $this->sObjectType;
    }

    /**
     * @return string
     */
    public function getSObjectId(): string
    {
        return $this->sObjectId;
    }

    public function getBasePath(): string
    {
        return "/services/data/v".$this->getVersion()."/sobjects/";
    }
}
