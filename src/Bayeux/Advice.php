<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 1:55 PM
 */

namespace AE\SalesforceRestSdk\Bayeux;

use JMS\Serializer\Annotation as JMS;

/**
 * Class Advice
 *
 * @package AE\SalesforceRestSdk\Bayeux
 * @JMS\ExclusionPolicy("NONE")
 */
class Advice
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $reconnect;

    /**
     * @var int
     * @JMS\Type("int")
     */
    private $timeout;

    /**
     * @var int
     * @JMS\Type("int")
     */
    private $interval;

    /**
     * @var array
     * @JMS\Type("array")
     */
    private $callbackPolling;

    /**
     * @return string
     */
    public function getReconnect(): ?string
    {
        return $this->reconnect;
    }

    /**
     * @param string $reconnect
     *
     * @return Advice
     */
    public function setReconnect(string $reconnect): Advice
    {
        $this->reconnect = $reconnect;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     *
     * @return Advice
     */
    public function setTimeout(int $timeout): Advice
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return int
     */
    public function getInterval(): ?int
    {
        return $this->interval;
    }

    /**
     * @param int $interval
     *
     * @return Advice
     */
    public function setInterval(int $interval): Advice
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @return array
     */
    public function getCallbackPolling(): ?array
    {
        return $this->callbackPolling;
    }

    /**
     * @param array $callbackPolling
     *
     * @return Advice
     */
    public function setCallbackPolling(array $callbackPolling): Advice
    {
        $this->callbackPolling = $callbackPolling;

        return $this;
    }
}
