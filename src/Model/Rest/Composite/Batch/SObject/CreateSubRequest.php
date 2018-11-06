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
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\CreateSubRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\CreateResponse;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class CreateSubRequest
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject
 * @Serializer\ExclusionPolicy("none")
 */
class CreateSubRequest extends PostSubRequest implements CreateSubRequestInterface
{
    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectType;

    public function __construct(string $sObjectType, ?SObject $sObject = null)
    {
        parent::__construct();

        $this->sObjectType = $sObjectType;
        $this->url         = 'v'.Client::VERSION.'/sobjects/'.$this->sObjectType.'/';
        $this->setRichInput($sObject);
    }

    final public function setRichInput($richInput): SubRequest
    {
        if ($richInput instanceof SObject) {
            $richInput->Id   = null;

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
     * @return string
     */
    public function getSObjectType(): string
    {
        return $this->sObjectType;
    }

    public function getResultClass(): ?string
    {
        return CreateResponse::class;
    }
}
