<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:54 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\PostSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\SObjectSubRequestInterface;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class CreateSubRequest extends PostSubRequest implements SObjectSubRequestInterface
{
    private $sObjectType;

    public function __construct(string $sObjectType)
    {
        parent::__construct();

        $this->sObjectType = $sObjectType;
    }

    final public function setRichInput($richInput): SubRequest
    {
        if ($richInput instanceof SObject) {
            $richInput->Id = null;
            $richInput->Type = null;

            parent::setRichInput($richInput);
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

        $this->url = 'v'.Client::VERSION.'/sobjects/'.$this->sObjectType.'/';
    }

    /**
     * @return string
     */
    public function getSObjectType(): string
    {
        return $this->sObjectType;
    }
}
