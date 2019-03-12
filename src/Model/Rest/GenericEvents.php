<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 2:00 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GenericEvents
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 */
class GenericEvents
{
    /**
     * @var ArrayCollection|GenericEvent[]
     * @Serializer\Type("ArrayCollection<AE\SalesforceRestSdk\Model\Rest\GenericEvent>")
     */
    private $pushEvents;

    /**
     * GenericEvents constructor.
     *
     * @param array $pushEvents
     */
    public function __construct(array $pushEvents = [])
    {
        $this->pushEvents = new ArrayCollection($pushEvents);
    }

    /**
     * @param array $pushEvents
     *
     * @return GenericEvents
     */
    public static function create(array $pushEvents = []): self
    {
        return new static($pushEvents);
    }

    /**
     * @return GenericEvent[]|ArrayCollection
     */
    public function getPushEvents()
    {
        return $this->pushEvents;
    }

    /**
     * @return GenericEvents
     */
    public function clearPushEvents(): GenericEvents
    {
        $this->pushEvents->clear();

        return $this;
    }

    /**
     * @param GenericEvent $event
     *
     * @return GenericEvents
     */
    public function addPushEvent(GenericEvent $event): GenericEvents
    {
        if (!$this->pushEvents->contains($event)) {
            $this->pushEvents->add($event);
        }

        return $this;
    }

    /**
     * @param GenericEvent $event
     *
     * @return GenericEvents
     */
    public function removePushEvent(GenericEvent $event): GenericEvents
    {
        if ($this->pushEvents->contains($event)) {
            $this->pushEvents->removeElement($event);
        }

        return $this;
    }

    /**
     * @param GenericEvent $event
     *
     * @return bool
     */
    public function hasPushEvent(GenericEvent $event): bool
    {
        return $this->pushEvents->contains($event);
    }
}
