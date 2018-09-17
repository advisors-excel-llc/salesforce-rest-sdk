<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 5:19 PM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

use AE\SalesforceRestSdk\Model\SObject;
use Doctrine\Common\Collections\Collection;

interface CompositeRequestInterface
{
    public function setRecords($records);

    /**
     * @return Collection|SObject[]
     */
    public function getRecords(): Collection;
    public function setAllOrNone(bool $allOrNone);
    public function isAllOrNone(): bool;
    public function addRecord(SObject $record);
    public function removeRecord(SObject $record);
}
