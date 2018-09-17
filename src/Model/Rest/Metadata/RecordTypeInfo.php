<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 4:17 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Metadata;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class RecordTypeInfo
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Metadata
 * @Serializer\ExclusionPolicy("none")
 */
class RecordTypeInfo
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $active;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $available;
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $defaultRecordTypeMapping;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $developerName;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $master = true;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $recordTypeId;

    /**
     * @var array
     * @Serializer\Type("array<string, string>")
     */
    private $urls = [];

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return RecordTypeInfo
     */
    public function setActive(bool $active): RecordTypeInfo
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     *
     * @return RecordTypeInfo
     */
    public function setAvailable(bool $available): RecordTypeInfo
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultRecordTypeMapping(): bool
    {
        return $this->defaultRecordTypeMapping;
    }

    /**
     * @param bool $defaultRecordTypeMapping
     *
     * @return RecordTypeInfo
     */
    public function setDefaultRecordTypeMapping(bool $defaultRecordTypeMapping): RecordTypeInfo
    {
        $this->defaultRecordTypeMapping = $defaultRecordTypeMapping;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDeveloperName(): ?string
    {
        return $this->developerName;
    }

    /**
     * @param null|string $developerName
     *
     * @return RecordTypeInfo
     */
    public function setDeveloperName(?string $developerName): RecordTypeInfo
    {
        $this->developerName = $developerName;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMaster(): bool
    {
        return $this->master;
    }

    /**
     * @param bool $master
     *
     * @return RecordTypeInfo
     */
    public function setMaster(bool $master): RecordTypeInfo
    {
        $this->master = $master;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return RecordTypeInfo
     */
    public function setName(?string $name): RecordTypeInfo
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRecordTypeId(): ?string
    {
        return $this->recordTypeId;
    }

    /**
     * @param null|string $recordTypeId
     *
     * @return RecordTypeInfo
     */
    public function setRecordTypeId(?string $recordTypeId): RecordTypeInfo
    {
        $this->recordTypeId = $recordTypeId;

        return $this;
    }

    /**
     * @return array
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    /**
     * @param array $urls
     *
     * @return RecordTypeInfo
     */
    public function setUrls(array $urls): RecordTypeInfo
    {
        $this->urls = $urls;

        return $this;
    }
}
