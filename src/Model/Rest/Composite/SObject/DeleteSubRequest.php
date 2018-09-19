<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 5:39 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\DeleteSubRequest as BaseSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
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

    public function __construct(string $sObjectType, string $sObjectId, ?string $referenceId = null)
    {
        parent::__construct($referenceId);

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

        $this->url = '/services/data/v.'.Client::VERSION.'/sobjects/'.$this->sObjectType.'/'.$this->sObjectId;
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
}
