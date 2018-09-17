<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 5:28 PM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

interface CompositeResponseInterface
{
    public function getId(): ?string;
    public function isSuccess(): bool;
    public function getErrors(): ?array;
    public function getWarnings(): ?array;
}
