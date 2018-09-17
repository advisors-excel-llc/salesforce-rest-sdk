<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 3:27 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class Limit
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 * @Serializer\ExclusionPolicy("none")
 */
class Limit
{
    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $Max = 0;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $Remaining = 0;

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->Max;
    }

    /**
     * @param int $Max
     *
     * @return Limit
     */
    public function setMax(int $Max): Limit
    {
        $this->Max = $Max;

        return $this;
    }

    /**
     * @return int
     */
    public function getRemaining(): int
    {
        return $this->Remaining;
    }

    /**
     * @param int $Remaining
     *
     * @return Limit
     */
    public function setRemaining(int $Remaining): Limit
    {
        $this->Remaining = $Remaining;

        return $this;
    }
}
