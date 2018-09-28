<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/7/18
 * Time: 5:45 PM
 */

namespace AE\SalesforceRestSdk\Bayeux\Salesforce;

use AE\SalesforceRestSdk\Model\SObject;
use JMS\Serializer\Annotation as JMS;

/**
 * Class StreamingData
 *
 * @package AE\SalesforceRestSdk\Bayeux\Salesforce
 * @JMS\ExclusionPolicy("NONE")
 */
class StreamingData
{
    /**
     * @var Event
     * @JMS\Type("AE\SalesforceRestSdk\Bayeux\Salesforce\Event")
     */
    private $event;

    /**
     * @var SObject|null
     * @JMS\Type("AE\SalesforceRestSdk\Model\SObject")
     */
    private $sobject;

    /**
     * @var mixed
     * @JMS\Type("array")
     */
    private $payload;

    /**
     * @var null|string
     * @JMS\Type("string")
     */
    private $schema;

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     *
     * @return StreamingData
     */
    public function setEvent(Event $event): StreamingData
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return SObject|null
     */
    public function getSobject(): ?SObject
    {
        return $this->sobject;
    }

    /**
     * @param SObject|null $sobject
     *
     * @return StreamingData
     */
    public function setSobject(?SObject $sobject): StreamingData
    {
        $this->sobject = $sobject;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     *
     * @return StreamingData
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSchema(): ?string
    {
        return $this->schema;
    }

    /**
     * @param null|string $schema
     *
     * @return StreamingData
     */
    public function setSchema(?string $schema): StreamingData
    {
        $this->schema = $schema;

        return $this;
    }
}
