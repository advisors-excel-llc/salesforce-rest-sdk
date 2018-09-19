<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 10:52 AM
 */

namespace AE\SalesforceRestSdk\Rest\Composite\Builder;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeRequest;

interface RequestBuilderInterface
{
    public function build(): CompositeRequest;
}
