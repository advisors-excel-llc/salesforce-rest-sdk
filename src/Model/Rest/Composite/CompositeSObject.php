<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 6:03 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use AE\SalesforceRestSdk\Model\SObject;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class CompositeSObject
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite
 * @Serializer\ExclusionPolicy("none")
 */
class CompositeSObject extends SObject
{
    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $attributes = ['type' => 'sobject'];

    public function __construct(string $type = 'sobject', array $fields = [])
    {
        parent::__construct($fields);
        $this->attributes['type'] = $type;
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected static function normalizeFieldName($name): string
    {
        if ($name !== "id") {
            return parent::normalizeFieldName($name);
        }

        if ($name === 'Id') {
            return'id';
        }

        return $name;
    }

    public function getType(): string
    {
        return $this->attributes['type'];
    }

    public function setType(string $type): CompositeSObject
    {
        $this->attributes['type'] = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return array_key_exists('url', $this->attributes) ? $this->attributes['url'] : null;
    }

    public function setUrl(?string $url): CompositeSObject
    {
        $this->attributes['url'] = $url;

        return $this;
    }

    public function setReferenceId(string $refId): CompositeSObject
    {
        $this->attributes["referenceId"] = $refId;

        return $this;
    }

    public function getReferenceId(): ?string
    {
        return array_key_exists("referenceId", $this->attributes) ? $this->attributes["referenceId"] : null;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function __set($name, $value)
    {
        if ('url' === strtolower($name)) {
            $this->setUrl($value);
        } elseif ("referenceid" === strtolower($name)) {
            $this->setReferenceId($value);
        } else {
            parent::__set($name, $value);
        }
    }

    public function __get($name)
    {
        if ('url' === strtolower($name)) {
            return $this->getUrl();
        }

        if ('referenceid' === strtolower($name)) {
            return $this->getReferenceId();
        }

        return parent::__get($name);
    }
}
