<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 12:14 PM
 */
namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Limit;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\Limits;
use AE\SalesforceRestSdk\Rest\Client;

class LimitsSubRequest extends GetSubRequest
{
    protected $url = 'v'.Client::VERSION.'/limits';

    final public function setRichInput($richInput): SubRequest
    {
        return $this;
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    public function getResultClass(): ?string
    {
        return Limits::class;
    }
}
