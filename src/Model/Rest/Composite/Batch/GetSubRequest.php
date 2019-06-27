<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:05 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch;

class GetSubRequest extends SubRequest
{
    public function __construct(string $version = "44.0")
    {
        parent::__construct("GET", $version);
    }
}
