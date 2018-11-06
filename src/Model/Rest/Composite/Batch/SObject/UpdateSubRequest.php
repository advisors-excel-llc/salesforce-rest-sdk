<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:36 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\PatchSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\SObjectSubRequestInterface;
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
     * @param string $sObjectType
     * @param null|string|SObject $sObject
     */
    public function __construct(string $sObjectType, $sObject = null)
    {
        parent::__construct();

        $this->sObjectType = $sObjectType;

        if (null !== $sObject && is_string($sObject)) {
            $this->sObjectId = $sObject;
        } elseif (null !== $sObject && $sObject instanceof SObject) {
            $this->setRichInput($sObject);
        }
    }

    final public function setRichInput($richInput): SubRequest
    {
        if ($richInput instanceof SObject) {
            parent::setRichInput($richInput);

            if (null !== $richInput->Id) {
                $this->sObjectId = $richInput->Id;
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
        if (null === $this->sObjectType || null === $this->sObjectId || null === $this->getRichInput()) {
            throw new \RuntimeException("The UpdateSubRequest is incomplete.");
        }

        $this->url = 'v'.Client::VERSION.'/sobjects/'.$this->sObjectType.'/'.$this->sObjectId;

        $this->richInput->Id   = null;
    }

    public function postSerialize()
    {
        $this->richInput->Id   = $this->sObjectId;
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

    /**
     * @param string $sObjectId
     *
     * @return UpdateSubRequest
     */
    public function setSObjectId(string $sObjectId): UpdateSubRequest
    {
        $this->sObjectId = $sObjectId;

        return $this;
    }
}
