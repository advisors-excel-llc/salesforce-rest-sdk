<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 11:43 AM
 */

namespace AE\SalesforceRestSdk\Bayeux;

use AE\SalesforceRestSdk\Bayeux\Extension\ExtensionInterface;

interface ChannelInterface
{
    public const META             = '/meta';
    public const META_HANDSHAKE   = self::META.'/handshake';
    public const META_CONNECT     = self::META.'/connect';
    public const META_DISCONNECT  = self::META.'/disconnect';
    public const META_SUBSCRIBE   = self::META.'/subscribe';
    public const META_UNSUBSCRIBE = self::META.'/unsubscribe';

    public function notifyMessageListeners(Message $message);
    public function getChannelId(): string;
    public function subscribe(ConsumerInterface $consumer);
    public function unsubscribe(ConsumerInterface $consumer);
    public function unsubscribeAll();
    public function isMeta();
    public function addExtension(ExtensionInterface $extension);
    public function hasExtension(string $name): bool;
    public function prepareOutgoingMessage(Message $message);
}
