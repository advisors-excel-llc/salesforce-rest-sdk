<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 12:50 PM
 */

namespace AE\SalesforceRestSdk\Tests\Bayeux\Extensions;

use AE\SalesforceRestSdk\Bayeux\Channel;
use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Extension\CachedReplayExtension;
use AE\SalesforceRestSdk\Bayeux\Message;
use AE\SalesforceRestSdk\Bayeux\Salesforce\Event;
use AE\SalesforceRestSdk\Bayeux\Salesforce\StreamingData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CachedReplayExtensionTest extends TestCase
{
    public function testOutgoing()
    {
        $adapter = new ArrayAdapter();
        $channel = new Channel(ChannelInterface::META_SUBSCRIBE);
        $ext     = new CachedReplayExtension($adapter);
        $now     = new \DateTime();

        $channel->addExtension($ext);

        $this->assertEquals(CachedReplayExtension::REPLAY_NEWEST, $ext->getReplayIdForChannel('/topic/cool_topic'));

        $key  = "REPLAY_EXT__topic_cool_topic";
        $item = $adapter->getItem($key);
        $item->set(
            [
                'replayId'  => 500,
                'timestamp' => $now->format(\DATE_ISO8601),
            ]
        );
        $item->expiresAfter(new \DateInterval('P1D'));
        $adapter->save($item);

        $message = new Message();
        $message->setChannel(ChannelInterface::META_SUBSCRIBE)
                ->setSubscription('/topic/cool_topic')
        ;

        $channel->prepareOutgoingMessage($message);

        $this->assertEquals(
            [
                CachedReplayExtension::NAME => [
                    '/topic/cool_topic' => 500,
                ],
            ],
            $message->getExt()
        );

        $item->expiresAt($now->sub(new \DateInterval('P1D')));
        $adapter->save($item);

        $channel->prepareOutgoingMessage($message);

        $this->assertEquals(
            [
                CachedReplayExtension::NAME => [
                    '/topic/cool_topic' => CachedReplayExtension::REPLAY_NEWEST,
                ],
            ],
            $message->getExt()
        );
    }

    public function testIncoming()
    {
        $adapter = new ArrayAdapter();
        $channel = new Channel('/topic/cool_topic');
        $ext     = new CachedReplayExtension($adapter);
        $now     = new \DateTimeImmutable("now", new \DateTimeZone("UTC"));
        $key     = "REPLAY_EXT__topic_cool_topic";

        $channel->addExtension($ext);

        $this->assertEquals(CachedReplayExtension::REPLAY_NEWEST, $ext->getReplayIdForChannel('/topic/cool_topic'));

        $event = new Event();
        $event->setReplayId(500);

        $data = new StreamingData();
        $data->setEvent($event);

        $message = new Message();
        $message->setChannel('/topic/cool_topic')
                ->setData($data)
                ->setTimestamp($now)
        ;

        $channel->notifyMessageListeners($message);

        $this->assertEquals(500, $ext->getReplayIdForChannel('/topic/cool_topic'));
        $this->assertTrue($adapter->hasItem($key));

        $item = $adapter->getItem($key);
        $value = $item->get();

        $this->assertEquals($now->format(\DATE_ISO8601), $value['timestamp']);
        $this->assertEquals(500, $value['replayId']);
    }
}
