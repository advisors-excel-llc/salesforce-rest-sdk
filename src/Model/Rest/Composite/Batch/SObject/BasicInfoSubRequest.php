<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 4:32 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\BasicInfoRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\Metadata\BasicInfo;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class BasicInfoSubRequest extends GetSubRequest implements BasicInfoRequestInterface
{
    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectType;

    public function __construct(string $sObjectType)
    {
        parent::__construct();

        $this->sObjectType = $sObjectType;
        $this->url         = 'v'.Client::VERSION.'/sobjects/'.$this->sObjectType;
    }

    /**
     * @return string
     */
    public function getSObjectType(): string
    {
        return $this->sObjectType;
    }

    final public function setRichInput($body): SubRequest
    {
        return $this;
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    public function getResultClass(): ?string
    {
        return BasicInfo::class;
    }
}
