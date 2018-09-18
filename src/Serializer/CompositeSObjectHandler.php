<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 6:13 PM
 */

namespace AE\SalesforceRestSdk\Serializer;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class CompositeSObjectHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'type'   => CompositeSObject::class,
                'format' => 'json',
            ],
        ];
    }

    public function serializeCompositeSObjectTojson(
        JsonSerializationVisitor $visitor,
        CompositeSObject $sobject,
        array $type,
        Context $context
    ): array {
        $object = [
            'attributes' => ['type' => $sobject->getType()],
        ];

        foreach ($sobject->getFields() as $field => $value) {
            if (null === $value) {
                continue;
            }
            $object[$field] = $value;
        }

        if (null === $visitor->getRoot()) {
            $visitor->setRoot($object);
        } elseif (is_array($visitor->getRoot())) {
            $visitor->setData(null, $object);
        }

        return $object;
    }

    public function deserializeCompositeSObjectFromjson(
        JsonDeserializationVisitor $visitor,
        array $data,
        array $type,
        DeserializationContext $context
    ) {
        $sobject = new CompositeSObject();

        if (array_key_exists('attributes', $data)) {
            if (array_key_exists('type', $data['attributes'])) {
                $sobject->setType($data['attributes']['type']);
            }

            if (array_key_exists('url', $data['attributes'])) {
                $sobject->setUrl($data['attributes']['url']);
            }
        }

        foreach ($data as $field => $value) {
            $sobject->$field = $value;
        }

        return $sobject;
    }
}
