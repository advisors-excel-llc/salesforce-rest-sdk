<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:04 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch;

class PostSubRequest extends SubRequest
{
    public function __construct(string $version = "44.0")
    {
        parent::__construct("POST", $version);
    }
}
