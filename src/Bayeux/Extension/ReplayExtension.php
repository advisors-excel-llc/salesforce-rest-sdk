<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/11/18
 * Time: 12:06 PM
 */

namespace AE\SalesforceRestSdk\Bayeux\Extension;

use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Message;

class ReplayExtension implements ExtensionInterface
{
    public const REPLAY_NEWEST = -1;
    public const REPLAY_SAVED  = -2;
    public const NAME          = 'replay';
    public const REPLAY_ID_KEY = 'replayId';

    private $dataMap = [];

    /**
     * @var int
     */
    private $replayId = self::REPLAY_NEWEST;

    public function __construct(int $replayId = self::REPLAY_NEWEST)
    {
        $this->replayId = $replayId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @return int
     */
    public function getReplayId(): int
    {
        return $this->replayId;
    }

    /**
     * @param string $channelName
     *
     * @return int|mixed
     */
    public function getReplayIdForChannel(string $channelName)
    {
        return array_key_exists($channelName, $this->dataMap) ? $this->dataMap[$channelName] : $this->replayId;
    }

    /**
     * @param string $channelName
     * @param int $replayId
     *
     * @return ReplayExtension
     */
    public function setReplayIdForChannel(string $channelName, int $replayId = self::REPLAY_NEWEST): self
    {
        $this->dataMap[$channelName] = $replayId;

        return $this;
    }

    /**
     * @param Message $message
     */
    public function prepareSend(Message $message): void
    {
        if ($message->getChannel() === ChannelInterface::META_SUBSCRIBE) {
            $ext = $message->getExt() ?: [];
            $ext[static::NAME] = [
                $message->getSubscription() => $this->getReplayIdForChannel($message->getChannel()),
            ];

            $message->setExt($ext);
        }
    }

    /**
     * @param Message $message
     */
    public function processReceive(Message $message): void
    {
        if (!$message->isMeta()) {
            $data = $message->getData();

            if (null !== $data) {
                $event = $data->getEvent();

                if (null !== $event && null !== $event->getReplayId()) {
                    $this->persistReplayId($message);
                }
            }
        }
    }

    /**
     * @param Message $message
     */
    protected function persistReplayId(Message $message)
    {
        $event = $message->getData()->getEvent();
        $this->setReplayIdForChannel($message->getChannel(), $event->getReplayId());
    }
}
