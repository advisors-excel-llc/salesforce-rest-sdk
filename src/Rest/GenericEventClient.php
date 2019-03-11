<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 1:41 PM
 */

namespace AE\SalesforceRestSdk\Rest;

use AE\SalesforceRestSdk\Model\Rest\GenericEvent;
use AE\SalesforceRestSdk\Model\Rest\GenericEvents;
use AE\SalesforceRestSdk\Model\Rest\StreamingChannelResponse;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class GenericEventClient
{
    private const CACHE_KEY = "GENERIC_EVENTS";
    /**
     * @var Client
     */
    private $client;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * GenericEventClient constructor.
     *
     * @param AdapterInterface $adapter
     * @param Client $client
     */
    public function __construct(AdapterInterface $adapter, Client $client)
    {
        $this->adapter = $adapter;
        $this->client  = $client;
    }

    /**
     * @param bool $cached
     *
     * @return array
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getStreamingChannelIds($cached = true): array
    {
        if ($cached && $this->adapter->hasItem(self::CACHE_KEY)) {
            return $this->adapter->getItem(self::CACHE_KEY)->get();
        }

        $result = $this->client->query("SELECT Id, Name FROM StreamingChannel");
        $records = [];

        do {
            foreach ($result->getRecords() as $record) {
                $records[$record->Name] = $record->Id;
            }

            if (!$result->isDone()) {
                $result = $this->client->query($result);
            }
        } while (!$result->isDone());

        $item = $this->adapter->getItem(self::CACHE_KEY);
        $item->set($records);
        $this->adapter->save($item);

        return $records;
    }

    /**
     * @param string $channelName
     * @param bool $cached
     *
     * @return null|string
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getStreamingChannelId(string $channelName, bool $cached = true): ?string
    {
        $channels = $this->getStreamingChannelIds($cached);

        if (array_key_exists($channelName, $channels)) {
            return $channels[$channelName];
        }

        return null;
    }

    /**
     * @param string $channel
     * @param GenericEvent $event
     * @param bool $cached
     *
     * @return StreamingChannelResponse|null
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function sendEvent(string $channel, GenericEvent $event, $cached = true): ?StreamingChannelResponse
    {
        $channelId = $this->getStreamingChannelId($channel, $cached);

        if (null === $channelId) {
            return null;
        }

        $events = new GenericEvents();
        $events->addPushEvent($event);

        return $this->client->sendGenericEvents($channelId, $events);
    }

    /**
     * @param string $channel
     * @param GenericEvents $events
     * @param bool $cached
     *
     * @return StreamingChannelResponse
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function sendEvents(string $channel, GenericEvents $events, $cached = true): ?StreamingChannelResponse
    {
        $channelId = $this->getStreamingChannelId($channel, $cached);

        if (null === $channelId) {
            return null;
        }

        return $this->client->sendGenericEvents($channelId, $events);
    }

    /**
     * @param string $channel
     * @param bool $cached
     *
     * @return array
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getChannelSubscribers(string $channel, $cached = true): array
    {
        $channelId = $this->getStreamingChannelId($channel, $cached);

        if (null === $channelId) {
            return [];
        }

        return $this->client->getStreamingChannelSubscribers($channelId);
    }
}
