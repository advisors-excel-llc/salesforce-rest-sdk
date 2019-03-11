<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 9:59 AM
 */

namespace AE\SalesforceRestSdk\Bayeux;

use AE\SalesforceRestSdk\Bayeux\Extension\ExtensionInterface;
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

    /**
     * @var ArrayCollection|ExtensionInterface[]
     */
    private $extensions;

    public function __construct(string $channelId)
    {
        $this->channelId   = $channelId;
        $this->subscribers = new ArrayCollection();
        $this->extensions  = new ArrayCollection();
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

        usort(
            $subscribers,
            function (ConsumerInterface $a, ConsumerInterface $b) use ($subscribers) {
                $aP = $a->getPriority() ?: count($subscribers);
                $bP = $b->getPriority() ?: count($subscribers);

                if ($bP === $aP) {
                    return 0;
                }

                return $aP > $bP ? 1 : -1;
            }
        );

        $this->extensions->forAll(function (string $name, ExtensionInterface $extension) use ($message) {
            $extension->processReceive($message);
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

    public function addExtension(ExtensionInterface $extension)
    {
        if (!$this->hasExtension($extension->getName())) {
            $this->extensions->set($extension->getName(), $extension);
        }

        return $this;
    }

    public function hasExtension(string $name): bool
    {
        return $this->extensions->containsKey($name);
    }

    public function prepareOutgoingMessage(Message $message)
    {
        $this->extensions->forAll(function (string $name, ExtensionInterface $extension) use ($message) {
            $extension->prepareSend($message);
        });

        return $this;
    }
}
