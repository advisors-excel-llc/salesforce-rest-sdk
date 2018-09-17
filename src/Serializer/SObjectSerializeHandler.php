<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 1:30 PM
 */

namespace AE\SalesforceRestSdk\Serializer;

use AE\SalesforceRestSdk\Model\SObject;
use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\PropertyMetadata;

class SObjectSerializeHandler implements SubscribingHandlerInterface
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
        $object = [
            'attributes' => ['type' => $sobject->getType()],
        ];

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

        $current = $visitor->getCurrentObject();

        if (null === $current || $current instanceof SObject) {
            $visitor->setCurrentObject($sobject);
        } else {
            $refl         = new \ReflectionClass(get_class($visitor));
            $accessorProp = $refl->getProperty('accessor');
            $accessorProp->setAccessible(true);
            /** @var AccessorStrategyInterface $accessor */
            $accessor = $accessorProp->getValue($visitor);

            $paths    = $context->getCurrentPath();
            $property = array_pop($paths);

            /** @var PropertyMetadata $metadata */
            $metadata = $context->getMetadataFactory()
                                ->getMetadataForClass(get_class($current))
                                ->propertyMetadata[$property];

            $accessor->setValue(
                $current,
                $sobject,
                $metadata
            );
        }

        return $sobject;
    }
}
