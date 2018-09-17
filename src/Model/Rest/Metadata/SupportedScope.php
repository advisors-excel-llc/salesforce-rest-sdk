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
 * Class SupportedScope
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Metadata
 * @Serializer\ExclusionPolicy("none")
 */
class SupportedScope
{
    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $label;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $name;

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
     * @return SupportedScope
     */
    public function setLabel(?string $label): SupportedScope
    {
        $this->label = $label;

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
     * @return SupportedScope
     */
    public function setName(?string $name): SupportedScope
    {
        $this->name = $name;

        return $this;
    }
}
