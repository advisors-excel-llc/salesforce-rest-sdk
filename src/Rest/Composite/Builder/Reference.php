<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 11:34 AM
 */

namespace AE\SalesforceRestSdk\Rest\Composite\Builder;

class Reference
{
    private $referenceId;

    public function __construct(string $referenceId)
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return mixed
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    public function field(string $field)
    {
        return "@{{$this->referenceId}.$field}";
    }
}
