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
use AE\SalesforceRestSdk\Model\Rest\Metadata\DescribeSObject;
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
        string $version = "44.0",
        ?string $referenceId = null
    ) {
        parent::__construct($version, $referenceId);

        $this->sObjectType = $sObjectType;
        $this->url         = $this->getBasePath().$this->sObjectType.'/describe';
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

    public function getResultClass(): ?string
    {
        return DescribeSObject::class;
    }

    public function getBasePath(): string
    {
        return "/services/data/v".$this->getVersion()."/sobjects/";
    }
}
