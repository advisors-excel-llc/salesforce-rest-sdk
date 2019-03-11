<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 10:49 AM
 */

namespace AE\SalesforceRestSdk\Bayeux\Extension;

use AE\SalesforceRestSdk\Bayeux\Message;
use Psr\Cache\CacheItemPoolInterface;

class CachedReplayExtension extends ReplayExtension
{
    private const CACHE_PREFIX = "REPLAY_EXT_";

    /**
     * @var CacheItemPoolInterface
     */
    private $adapter;

    public function __construct(CacheItemPoolInterface $adapter, int $replayId = self::REPLAY_NEWEST)
    {
        parent::__construct($replayId);
        $this->adapter = $adapter;
    }

    public function getReplayIdForChannel(string $channelName)
    {
        $key = self::CACHE_PREFIX.preg_replace('/[\{\}\(\)\/\@]+/', '_', $channelName);
        if ($this->adapter->hasItem($key)) {
            $cache = $this->adapter->getItem($key)->get();
            if (is_array($cache) && array_key_exists('timestamp', $cache) && array_key_exists('replayId', $cache)) {
                $timestamp = \DateTime::createFromFormat(
                    \DATE_ISO8601,
                    $cache['timestamp'],
                    new \DateTimeZone("UTC")
                );
                $now       = new \DateTime();

                if (false == $now->diff($timestamp)->days) {
                    return $cache['replayId'];
                }
            }
        }

        return $this->getReplayId();
    }

    protected function persistReplayId(Message $message)
    {
        $key = self::CACHE_PREFIX.preg_replace('/[\{\}\(\)\/\@]+/', '_', $message->getChannel());
        $replayId  = $message->getData()->getEvent()->getReplayId();
        $timestamp = $message->getTimestamp() ?: new \DateTime();
        $item      = $this->adapter->getItem($key);
        $item->set(
            [
                'replayId'  => $replayId,
                'timestamp' => $timestamp->format(\DATE_ISO8601),
            ]
        );
        $item->expiresAfter(new \DateInterval('P1D'));

        $this->adapter->save($item);
    }
}
