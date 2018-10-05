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
            if (null === $value) {
                continue;
            }

            if (is_object($value)) {
                $className = get_class($value);
                if (false !== $className) {
                    if (\DateTime::class === $className) {
                        $object[$field] = $context->getNavigator()->accept(
                            $visitor->prepare($value),
                            ['name' => 'DateTime', 'params' => [\DATE_ISO8601, 'UTC']],
                            $context
                        )
                        ;
                    } elseif (\DateTimeImmutable::class === $className) {
                        $object[$field] = $context->getNavigator()->accept(
                            $visitor->prepare($value),
                            ['name' => 'DateTimeImmutable', 'params' => [\DATE_ISO8601, 'UTC']],
                            $context
                        )
                        ;
                    } else {
                        $object[$field] = $context->getNavigator()->accept(
                            $visitor->prepare($value),
                            ['name' => $className],
                            $context
                        )
                        ;
                    }
                } else {
                    $object[$field] = $value;
                }
            } else {
                $object[$field] = $value;
            }
        }

        // If the visitor's root is the value of the last field processed, we need to fix the root
        if ($object[$field] === $visitor->getRoot()) {
            $visitor->setRoot($object);
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
