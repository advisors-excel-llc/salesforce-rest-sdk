<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:32 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

interface ReferenceableInterface
{
    public function reference(string $fieldName);
}
