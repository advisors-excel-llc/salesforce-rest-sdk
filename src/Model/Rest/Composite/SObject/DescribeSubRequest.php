<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 2:47 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class DescribeSubRequest extends GetSubRequest implements DescribeSubRequestInterface
{
    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectType;

    public function __construct(
        string $sObjectType,
        ?string $referenceId = null
    ) {
        parent::__construct($referenceId);

        $this->sObjectType = $sObjectType;
    }

    /**
     * @return string
     */
    public function getSObjectType(): string
    {
        return $this->sObjectType;
    }

    final public function setBody($body): SubRequest
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
        $this->url = Client::BASE_PATH.$this->sObjectType.'/describe';
    }
}
