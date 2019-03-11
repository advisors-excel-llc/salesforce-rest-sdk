<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 2:46 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

class StreamingChannelResponse
{
    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $fanoutCount;

    /**
     * @var array|null
     * @Serializer\Type("array")
     */
    private $userOnlineStatus = [];

    /**
     * @return int|null
     */
    public function getFanoutCount(): ?int
    {
        return $this->fanoutCount;
    }

    /**
     * @param int|null $fanoutCount
     *
     * @return StreamingChannelResponse
     */
    public function setFanoutCount(?int $fanoutCount): StreamingChannelResponse
    {
        $this->fanoutCount = $fanoutCount;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getUserOnlineStatus(): ?array
    {
        return $this->userOnlineStatus;
    }

    /**
     * @param array|null $userOnlineStatus
     *
     * @return StreamingChannelResponse
     */
    public function setUserOnlineStatus(?array $userOnlineStatus): StreamingChannelResponse
    {
        $this->userOnlineStatus = $userOnlineStatus;

        return $this;
    }
}
