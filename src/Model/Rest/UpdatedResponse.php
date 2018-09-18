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
     * @var \DateTime
     * @Serializer\Type("datetime")
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
     * @return \DateTime
     */
    public function getLatestDateCovered(): \DateTime
    {
        return $this->latestDateCovered;
    }

    /**
     * @param \DateTime $latestDateCovered
     *
     * @return UpdatedResponse
     */
    public function setLatestDateCovered(\DateTime $latestDateCovered): UpdatedResponse
    {
        $this->latestDateCovered = $latestDateCovered;

        return $this;
    }
}
