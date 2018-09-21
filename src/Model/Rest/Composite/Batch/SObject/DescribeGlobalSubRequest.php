<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 2:53 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\DescribeGlobalSubRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Metadata\GlobalDescribe;
use AE\SalesforceRestSdk\Rest\SObject\Client;

class DescribeGlobalSubRequest extends GetSubRequest implements DescribeGlobalSubRequestInterface
{
    protected $url = 'v'.Client::VERSION.'/sobjects/';

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
        return GlobalDescribe::class;
    }
}
