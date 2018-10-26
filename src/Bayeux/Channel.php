<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 9:59 AM
 */

namespace AE\SalesforceRestSdk\Bayeux;

use Doctrine\Common\Collections\ArrayCollection;

class Channel implements ChannelInterface
{
    /**
     * @var string
     */
    private $channelId;

    /**
     * @var ArrayCollection|ConsumerInterface[]
     */
    private $subscribers;

    public function __construct(string $channelId)
    {
        $this->channelId   = $channelId;
        $this->subscribers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function notifyMessageListeners(Message $message)
    {
        /** @var ConsumerInterface[] $subscribers */
        $subscribers = $this->subscribers->getValues();

        usort($subscribers, function (ConsumerInterface $a, ConsumerInterface $b) use ($subscribers) {
            $aP = $a->getPriority() ?: count($subscribers);
            $bP = $b->getPriority() ?: count($subscribers);

            if ($bP === $aP) {
                return 0;
            }

            return $aP > $bP ? 1 : -1;
        });

        foreach ($subscribers as $consumer) {
            $consumer->consume($this, $message);
        }
    }

    public function subscribe(ConsumerInterface $consumer)
    {
        if (!$this->subscribers->contains($consumer)) {
            $this->subscribers->add($consumer);
        }
    }

    public function unsubscribe(ConsumerInterface $consumer)
    {
        if ($this->subscribers->contains($consumer)) {
            $this->subscribers->removeElement($consumer);
        }
    }

    public function unsubscribeAll()
    {
        $this->subscribers->clear();
    }

    public function isMeta()
    {
        return substr($this->channelId, 0, strlen(self::META)) === self::META;
    }
}
