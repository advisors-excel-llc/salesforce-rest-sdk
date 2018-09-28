<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 2:06 PM
 */

namespace AE\SalesforceRestSdk\Rest\Composite\Builder;

class ArrayReference extends Reference
{
    public function field(string $field, int $index = 0)
    {
        return "@{{$this->getReferenceId()}[$index].$field}";
    }
}
