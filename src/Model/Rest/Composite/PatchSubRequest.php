<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:05 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

class PatchSubRequest extends SubRequest
{
    public function __construct(?string $referenceId = null)
    {
        parent::__construct("PATCH", $referenceId);
    }
}
