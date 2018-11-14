<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 11/14/18
 * Time: 10:51 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class Count
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 */
class Count
{
    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $count = 0;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return Count
     */
    public function setCount(int $count): Count
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Count
     */
    public function setName(string $name): Count
    {
        $this->name = $name;

        return $this;
    }
}
