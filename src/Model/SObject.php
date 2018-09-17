<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 1:01 PM
 */

namespace AE\SalesforceRestSdk\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class SObject
 *
 * @package AE\SalesforceRestSdk\Model
 * @Serializer\ExclusionPolicy("NONE")
 */
class SObject
{
    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $attributes = ['type' => 'sobject'];

    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $fields = [];

    public function __construct(string $type = 'sobject', array $fields = [])
    {
        $this->attributes['type'] = $type;

        if (!empty($fields)) {
            foreach ($fields as $field => $value) {
                $this->$field = $value;
            }
        }
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected static function normalizeFieldName($name): string
    {
        if ($name !== "id") {
            $name = ucwords($name);
        } elseif ($name === 'Id') {
            $name = 'id';
        }

        return $name;
    }

    public function getType(): string
    {
        return $this->attributes['type'];
    }

    public function setType(string $type): SObject
    {
        $this->attributes['type'] = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return array_key_exists('url', $this->attributes) ? $this->attributes['url'] : null;
    }

    public function setUrl(?string $url): SObject
    {
        $this->attributes['url'] = $url;

        return $this;
    }

    public function setFields(array $fields): SObject
    {
        foreach ($fields as $field => $value) {
            $this->$field = $value;
        }

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function __set($name, $value)
    {
        if ('type' === $name) {
            $this->setType($value);
        } elseif ('url' === $name) {
            $this->setUrl($value);
        } else {
            $name = self::normalizeFieldName($name);
            $this->fields[$name] = $value;
        }
    }

    public function __get($name)
    {
        if ('type' === $name) {
            return $this->getType();
        }

        if ('url' === $name) {
            return $this->getUrl();
        }

        $name = self::normalizeFieldName($name);

        return array_key_exists($name, $this->fields) ? $this->fields[$name] : null;
    }

    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);

        if ("get" === $prefix) {
            $field = substr($name, 3);

            return $this->$field;
        }

        if ("set" === $prefix) {
            $field = substr($name, 3);

            $this->$field = array_shift($arguments);

            return $this;
        }
    }
}
