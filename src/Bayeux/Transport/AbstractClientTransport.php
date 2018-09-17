<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 11:45 AM
 */

namespace AE\SalesforceRestSdk\Bayeux\Transport;

use AE\SalesforceRestSdk\Bayeux\Message;
use AE\SalesforceRestSdk\Serializer\SObjectHandler;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

abstract class AbstractClientTransport
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->serializer = $this->createSerializer();
    }

    abstract public function abort();

    public function terminate()
    {
    }

    /**
     * @param Message[]|array $messages
     * @param callable|null $customize
     *
     * @return array|Message[]
     */
    abstract public function send($messages, ?callable $customize): array ;

    protected function parseMessages(string $context): array
    {
        return $this->serializer->deserialize($context, 'array<AE\\SalesforceRestSdk\\Bayeux\\Message>', 'json');
    }

    protected function generateJSON($messages): string
    {
        return $this->serializer->serialize($messages, 'json');
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return AbstractClientTransport
     */
    public function setUrl(?string $url): AbstractClientTransport
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    protected function createSerializer(): SerializerInterface
    {
        $builder = SerializerBuilder::create();
        $builder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
        $builder->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()));
        $builder->addDefaultHandlers()
                ->addDefaultListeners()
                ->addDefaultDeserializationVisitors()
                ->addDefaultSerializationVisitors()
                ->configureHandlers(
                    function (HandlerRegistry $handler) {
                        $handler->registerSubscribingHandler(new SObjectHandler());
                    }
                )
        ;

        return $builder->build();
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     *
     * @return AbstractClientTransport
     */
    public function setSerializer(SerializerInterface $serializer): AbstractClientTransport
    {
        $this->serializer = $serializer;

        return $this;
    }
}
