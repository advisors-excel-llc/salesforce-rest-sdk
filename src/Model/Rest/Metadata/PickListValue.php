<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 4:16 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Metadata;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class PickListValue
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Metadata
 * @Serializer\ExclusionPolicy("none")
 */
class PickListValue
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
    private $defaultValue;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $label;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $validFor;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $value;

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
     * @return PickListValue
     */
    public function setActive(bool $active): PickListValue
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultValue(): bool
    {
        return $this->defaultValue;
    }

    /**
     * @param bool $defaultValue
     *
     * @return PickListValue
     */
    public function setDefaultValue(bool $defaultValue): PickListValue
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param null|string $label
     *
     * @return PickListValue
     */
    public function setLabel(?string $label): PickListValue
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getValidFor(): ?string
    {
        return $this->validFor;
    }

    /**
     * @param null|string $validFor
     *
     * @return PickListValue
     */
    public function setValidFor(?string $validFor): PickListValue
    {
        $this->validFor = $validFor;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return PickListValue
     */
    public function setValue(string $value): PickListValue
    {
        $this->value = $value;

        return $this;
    }
}
