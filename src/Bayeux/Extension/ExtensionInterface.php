<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/11/18
 * Time: 12:03 PM
 */

namespace AE\SalesforceRestSdk\Bayeux\Extension;

use AE\SalesforceRestSdk\Bayeux\Message;

interface ExtensionInterface
{
    public function getName(): string;
    public function prepareSend(Message $message): void;
    public function processReceive(Message $message);
}
