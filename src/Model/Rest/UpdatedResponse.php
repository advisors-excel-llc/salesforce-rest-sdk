<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 10:26 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class UpdatedResponse
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 * @Serializer\ExclusionPolicy("none")
 */
class UpdatedResponse
{
    /**
     * @var array|string[]
     * @Serializer\Type("array<string>")
     */
    private $ids = [];

    /**
     * @var \DateTimeImmutable
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uO', 'UTC'>")
     */
    private $latestDateCovered;

    /**
     * @return array|string[]
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * @param array|string[] $ids
     *
     * @return UpdatedResponse
     */
    public function setIds($ids)
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLatestDateCovered(): \DateTimeImmutable
    {
        return $this->latestDateCovered;
    }

    /**
     * @param \DateTimeImmutable $latestDateCovered
     *
     * @return UpdatedResponse
     */
    public function setLatestDateCovered(\DateTimeImmutable $latestDateCovered): UpdatedResponse
    {
        $this->latestDateCovered = $latestDateCovered;

        return $this;
    }
}
