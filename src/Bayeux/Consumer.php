<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/10/18
 * Time: 5:16 PM
 */

namespace AE\SalesforceRestSdk\Bayeux;

class Consumer implements ConsumerInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var int|null
     */
    private $priority;

    public function __construct(callable $callable, ?int $priority = null)
    {
        $this->callable = $callable;
        $this->priority = $priority;
    }

    public function consume(ChannelInterface $channel, Message $message)
    {
        call_user_func($this->callable, $channel, $message);
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }


    public static function create(callable $callable, ?int $priority = null): Consumer
    {
        return new static($callable, $priority);
    }
}
