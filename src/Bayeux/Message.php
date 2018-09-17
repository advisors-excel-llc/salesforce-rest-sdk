<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 10:00 AM
 */

namespace AE\SalesforceRestSdk\Bayeux;

use AE\SalesforceRestSdk\Bayeux\Salesforce\StreamingData;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Class Message
 *
 * @package AE\SalesforceRestSdk\Bayeux
 * @JMS\ExclusionPolicy("NONE")
 */
class Message
{
    /**
     * @var string|null
     * @JMS\Type("string")
     * @JMS\Groups({"handshake", "subscribe", "unsubscribe", "connect", "disconnect"})
     */
    private $channel;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"connect", "disconnect", "subscribe", "unsubscribe", "disconnect"})
     */
    private $clientId;

    /**
     * @var StreamingData|null
     * @JMS\Type("AE\SalesforceRestSdk\Bayeux\Salesforce\StreamingData")
     */
    private $data;

    /**
     * @var string|null
     * @JMS\Type("string")
     * @JMS\Groups({"handshake"})
     */
    private $version;

    /**
     * @var string|null
     * @JMS\Type("string")
     * @JMS\Groups({"handshake"})
     */
    private $minimumVersion;

    /**
     * @var array|null
     * @JMS\Type("array<string>")
     * @JMS\Groups({"handshake"})
     */
    private $supportedConnectionTypes;

    /**
     * @var Advice|null
     * @JMS\Type("AE\SalesforceRestSdk\Bayeux\Advice")
     * @JMS\Exclude(if="context.getDirection() === 1")
     */
    private $advice;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"connect"})
     */
    private $connectionType;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"handshake", "connect", "subscribe", "unsubscribe", "disconnect"})
     */
    private $id;

    /**
     * @var \DateTimeImmutable|null
     * @JMS\Type("DateTimeImmutable<'Y-m-d\TH:i:s', 'GMT'>")
     */
    private $timestamp;

    /**
     * @var bool|null
     * @JMS\Type("bool")
     * @JMS\Exclude(if="context.getDirection() === 1")
     */
    private $successful;

    /**
     * @var bool|null
     * @JMS\Type("bool")
     * @JMS\Exclude(if="context.getDirection() === 1")
     */
    private $authSuccessful;

    /**
     * @var string|null
     * @JMS\Type("string")
     * @JMS\Groups({"subscribe", "unsubscribe"})
     */
    private $subscription;

    /**
     * @var string|null
     * @JMS\Type("string")
     * @JMS\Exclude(if="context.getDirection() === 1")
     */
    private $error;

    /**
     * @var array|null
     * @JMS\Type("array")
     * @JMS\Groups({"handshake", "connect", "disconnect", "subscribe", "unsubscribe"})
     */
    private $ext;

    /**
     * @return null|string
     */
    public function getChannel(): ?string
    {
        return $this->channel;
    }

    /**
     * @param null|string $channel
     *
     * @return Message
     */
    public function setChannel(?string $channel): Message
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     *
     * @return Message
     */
    public function setClientId(string $clientId): Message
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return StreamingData|null
     */
    public function getData(): ?StreamingData
    {
        return $this->data;
    }

    /**
     * @param StreamingData|null $data
     *
     * @return Message
     */
    public function setData(?StreamingData $data): Message
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param null|string $version
     *
     * @return Message
     */
    public function setVersion(?string $version): Message
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMinimumVersion(): ?string
    {
        return $this->minimumVersion;
    }

    /**
     * @param null|string $minimumVersion
     *
     * @return Message
     */
    public function setMinimumVersion(?string $minimumVersion): Message
    {
        $this->minimumVersion = $minimumVersion;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getSupportedConnectionTypes(): ?array
    {
        return $this->supportedConnectionTypes;
    }

    /**
     * @param array|null $supportedConnectionTypes
     *
     * @return Message
     */
    public function setSupportedConnectionTypes(?array $supportedConnectionTypes): Message
    {
        $this->supportedConnectionTypes = $supportedConnectionTypes;

        return $this;
    }

    /**
     * @return Advice|null
     */
    public function getAdvice(): ?Advice
    {
        return $this->advice;
    }

    /**
     * @param Advice|null $advice
     *
     * @return Message
     */
    public function setAdvice(?Advice $advice): Message
    {
        $this->advice = $advice;

        return $this;
    }

    /**
     * @return string
     */
    public function getConnectionType(): string
    {
        return $this->connectionType;
    }

    /**
     * @param string $connectionType
     *
     * @return Message
     */
    public function setConnectionType(string $connectionType): Message
    {
        $this->connectionType = $connectionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Message
     */
    public function setId(string $id): Message
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTimeImmutable|null $timestamp
     *
     * @return Message
     */
    public function setTimestamp(?\DateTimeImmutable $timestamp): Message
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isSuccessful(): ?bool
    {
        return $this->successful;
    }

    /**
     * @param bool|null $successful
     *
     * @return Message
     */
    public function setSuccessful(?bool $successful): Message
    {
        $this->successful = $successful;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAuthSuccessful(): ?bool
    {
        return $this->authSuccessful;
    }

    /**
     * @param bool|null $authSuccessful
     *
     * @return Message
     */
    public function setAuthSuccessful(?bool $authSuccessful): Message
    {
        $this->authSuccessful = $authSuccessful;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSubscription(): ?string
    {
        return $this->subscription;
    }

    /**
     * @param null|string $subscription
     *
     * @return Message
     */
    public function setSubscription(?string $subscription): Message
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param null|string $error
     *
     * @return Message
     */
    public function setError(?string $error): Message
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getExt(): ?array
    {
        return $this->ext;
    }

    /**
     * @param array|null $ext
     *
     * @return Message
     */
    public function setExt(?array $ext): Message
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * @JMS\PreSerialize()
     * @throws \Exception
     */
    public function preSerialize()
    {
        if (null === $this->timestamp) {
            $this->timestamp = new \DateTimeImmutable();
        }

        if (null === $this->id) {
            $this->id = Uuid::uuid4()->toString();
        }
    }

    public function isMeta(): bool
    {
        return ChannelInterface::META === substr($this->getChannel(), 0, strlen(ChannelInterface::META));
    }
}
