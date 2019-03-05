<?php declare(strict_types=1);

namespace AE\SalesforceRestSdk\Tests\Bayeux\Extension;

use AE\SalesforceRestSdk\Bayeux\Extension\ReplayExtension;
use AE\SalesforceRestSdk\Bayeux\Message;
use PHPUnit\Framework\TestCase;

class ReplayExtensionTest extends TestCase
{
    public function testPrepareSend(): void
    {
        $channelId = 'foo';
        $replayId = 42;
        $extension = new ReplayExtension($channelId, $replayId);

        $message = new Message();
        $message->setSubscription('foo');
        $extension->prepareSend($message);

        $this->assertEquals(['replay' => ['foo' => 42]], $message->getExt());
    }

    public function testPrepareSend_WithUnsupportedChannelId(): void
    {
        $channelId = 'foo';
        $replayId = 42;
        $extension = new ReplayExtension($channelId, $replayId);

        $message = new Message();
        $message->setSubscription('bar');
        $extension->prepareSend($message);

        $this->assertNull($message->getExt());
    }

    public function testPrepareSend_WithPreviousExtensionSet(): void
    {
        $channelId = 'foo';
        $replayId = 42;
        $extension = new ReplayExtension($channelId, $replayId);

        $message = new Message();
        $message->setSubscription('foo');
        $message->setExt(['foo' => 'bar']);
        $extension->prepareSend($message);

        $this->assertEquals(['foo' => 'bar', 'replay' => ['foo' => 42]], $message->getExt());
    }
}
