<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 5:39 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\DeleteSubRequest as BaseSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\SObjectSubRequestInterface;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class DeleteSubRequest extends BaseSubRequest implements SObjectSubRequestInterface
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

    public function __construct(string $sObjectType, string $sObjectId)
    {
        parent::__construct();

        $this->sObjectType = $sObjectType;
        $this->sObjectId   = $sObjectId;
    }

    final public function setRichInput($richInput): SubRequest
    {
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
        if (null === $this->sObjectType || null === $this->sObjectId) {
            throw new \RuntimeException("DeleteSubRequest is incomplete.");
        }

        $this->url = 'v.'.Client::VERSION.'/sobjects/'.$this->sObjectType.'/'.$this->sObjectId;
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
     * @return DeleteSubRequest
     */
    public function setSObjectId(string $sObjectId): DeleteSubRequest
    {
        $this->sObjectId = $sObjectId;

        return $this;
    }
}
