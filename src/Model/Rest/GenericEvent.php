<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 1:58 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

class GenericEvent
{
    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $payload;

    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $userIds = [];

    public function __construct(?string $payload = null, array $userIds = [])
    {
        $this->payload = $payload;
        $this->userIds = $userIds;
    }

    /**
     * @param null|string $payload
     * @param array $userIds
     *
     * @return GenericEvent
     */
    public static function create(?string $payload = null, array $userIds = []): self
    {
        return new static($payload, $userIds);
    }


    /**
     * @return null|string
     */
    public function getPayload(): ?string
    {
        return $this->payload;
    }

    /**
     * @param null|string $payload
     *
     * @return GenericEvent
     */
    public function setPayload(?string $payload): GenericEvent
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return array
     */
    public function getUserIds(): array
    {
        return $this->userIds;
    }

    /**
     * @param array $userIds
     *
     * @return GenericEvent
     */
    public function setUserIds(array $userIds): GenericEvent
    {
        $this->userIds = $userIds;

        return $this;
    }
}
