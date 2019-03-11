<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 12:30 PM
 */

namespace AE\SalesforceRestSdk\Tests\Bayeux\Extensions;

use AE\SalesforceRestSdk\Bayeux\Channel;
use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Extension\ReplayExtension;
use AE\SalesforceRestSdk\Bayeux\Message;
use AE\SalesforceRestSdk\Bayeux\Salesforce\Event;
use AE\SalesforceRestSdk\Bayeux\Salesforce\StreamingData;
use PHPUnit\Framework\TestCase;

class ReplayExtensionTest extends TestCase
{
    public function testOutgoing()
    {
        $channel = new Channel(ChannelInterface::META_SUBSCRIBE);
        $ext     = new ReplayExtension();

        $channel->addExtension($ext);

        $message = new Message();
        $message->setChannel(ChannelInterface::META_SUBSCRIBE)
                ->setSubscription('/topic/cool_topic')
        ;

        $channel->prepareOutgoingMessage($message);

        $this->assertEquals(
            [
                ReplayExtension::NAME => [
                    '/topic/cool_topic' => ReplayExtension::REPLAY_NEWEST,
                ],
            ],
            $message->getExt()
        );
    }

    public function testIncoming()
    {
        $channel = new Channel('/topic/cool_topic');
        $ext = new ReplayExtension();
        $channel->addExtension($ext);

        $event = new Event();
        $event->setReplayId(500);

        $data = new StreamingData();
        $data->setEvent($event);

        $message = new Message();
        $message->setChannel('/topic/cool_topic')
            ->setData($data)
            ;

        $channel->notifyMessageListeners($message);

        $this->assertEquals(500, $ext->getReplayIdForChannel('/topic/cool_topic'));
    }
}
