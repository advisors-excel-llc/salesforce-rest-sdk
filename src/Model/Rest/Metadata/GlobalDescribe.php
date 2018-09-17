<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 5:47 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Metadata;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class GlobalDescribe
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Metadata
 * @Serializer\ExclusionPolicy("none")
 */
class GlobalDescribe
{
    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $encoding;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $maxBatchSize;

    /**
     * @var array|DescribeSObject[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Metadata\DescribeSObject>")
     */
    private $sobjects = [];

    /**
     * @return null|string
     */
    public function getEncoding(): ?string
    {
        return $this->encoding;
    }

    /**
     * @param null|string $encoding
     *
     * @return GlobalDescribe
     */
    public function setEncoding(?string $encoding): GlobalDescribe
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxBatchSize(): int
    {
        return $this->maxBatchSize;
    }

    /**
     * @param int $maxBatchSize
     *
     * @return GlobalDescribe
     */
    public function setMaxBatchSize(int $maxBatchSize): GlobalDescribe
    {
        $this->maxBatchSize = $maxBatchSize;

        return $this;
    }

    /**
     * @return DescribeSObject[]|array
     */
    public function getSobjects()
    {
        return $this->sobjects;
    }

    /**
     * @param DescribeSObject[]|array $sobjects
     *
     * @return GlobalDescribe
     */
    public function setSobjects($sobjects)
    {
        $this->sobjects = $sobjects;

        return $this;
    }
}
