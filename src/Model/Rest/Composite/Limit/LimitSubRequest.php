<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 1:51 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Limit;

use AE\SalesforceRestSdk\Model\Rest\Composite\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Rest\Client;

class LimitSubRequest extends GetSubRequest implements LimitSubRequestInterface
{
    protected $url = '/services/data/v'.Client::VERSION.'/limits/';

    final public function setBody($body): SubRequest
    {
        return $this;
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }
}
