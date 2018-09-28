<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/27/18
 * Time: 6:14 PM
 */

namespace AE\SalesforceRestSdk\AuthProvider;

use Throwable;

class SessionExpiredOrInvalidException extends \Exception
{
    public function __construct(string $message, string $code, Throwable $previous = null)
    {
        parent::__construct("$code: $message", 0, $previous);
    }
}
