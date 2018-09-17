<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 1:30 PM
 */

namespace AE\SalesforceRestSdk\Serializer;

use AE\SalesforceRestSdk\Model\SObject;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class SObjectHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'type'   => SObject::class,
                'format' => 'json',
            ],
        ];
    }

    public function serializeSObjectTojson(
        JsonSerializationVisitor $visitor,
        SObject $sobject,
        array $type,
        Context $context
    ): array {

        foreach ($sobject->getFields() as $field => $value) {
            $object[$field] = $value;
        }

        if (null === $visitor->getRoot()) {
            $visitor->setRoot($object);
        } elseif (is_array($visitor->getRoot())) {
            $visitor->setData(null, $object);
        }

        return $object;
    }

    public function deserializeSObjectFromjson(
        JsonDeserializationVisitor $visitor,
        array $data,
        array $type,
        DeserializationContext $context
    ) {
        $sobject = new SObject();

        foreach ($data as $field => $value) {
            $sobject->$field = $value;
        }

        return $sobject;
    }
}
