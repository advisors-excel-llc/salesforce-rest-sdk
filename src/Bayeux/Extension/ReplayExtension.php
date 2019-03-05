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
    public const NAME = 'replay';

    /**
     * @var int
     */
    private $replayId = self::REPLAY_NEWEST;

    /**
     * @var string
     */
    private $channelId;

    public function __construct(string $channel, int $replayId = self::REPLAY_NEWEST)
    {
        $this->channelId = $channel;
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
     * @param Message $message
     */
    public function prepareSend(Message $message): void
    {
        if ($message->getSubscription() === $this->channelId) {
            $ext = $message->getExt() ?: [];
            $ext[$this->getName()] = [$this->channelId => $this->replayId];

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
                    $this->replayId = $event->getReplayId();
                }
            }
        }
    }
}
