<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:07 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

class DeleteSubRequest extends SubRequest
{
    public function __construct(?string $referenceId = null)
    {
        parent::__construct("DELETE", $referenceId);
    }
}
