<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/10/18
 * Time: 5:15 PM
 */

namespace AE\SalesforceRestSdk\Bayeux;

interface ConsumerInterface
{
    public function consume(ChannelInterface $channel, Message $message);
    public function getPriority(): ?int;
}