<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 9:58 AM
 */

namespace AE\SalesforceRestSdk\Bayeux;

use AE\SalesforceRestSdk\AuthProvider\AuthProviderInterface;
use AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException;
use AE\SalesforceRestSdk\Bayeux\Extension\ExtensionInterface;
use AE\SalesforceRestSdk\Bayeux\Transport\AbstractClientTransport;
use AE\SalesforceRestSdk\Bayeux\Transport\HttpClientTransport;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class BayeuxClient
{
    /**
     * State assumed after the handshake when the connection is broken
     */
    public const UNCONNECTED = "UNCONNECTED";
    /**
     * State assumed when the handshake is being sent
     */
    public const HANDSHAKING = "HANDSHAKING";
    /**
     * State assumed when a first handshake failed and the handshake is retried,
     * or when the Bayeux server requests a re-handshake
     */
    public const REHANDSHAKING = "REHANDSHAKING";
    /**
     * State assumed when the handshake is received, but before connecting
     */
    public const HANDSHAKEN = "HANDSHAKEN";
    /**
     * State assumed when the connect is being sent for the first time
     */
    public const CONNECTING = "CONNECTING";
    /**
     * State assumed when this {@link BayeuxClient} is connected to the Bayeux server
     */
    public const CONNECTED = "CONNECTED";
    /**
     * State assumed when the disconnect is being sent
     */
    public const DISCONNECTING = "DISCONNECTING";
    /**
     * State assumed when the disconnect is received but terminal actions must be performed
     */
    public const TERMINATING = "TERMINATING";
    /**
     * State assumed before the handshake and when the disconnect is completed
     */
    public const DISCONNECTED = "DISCONNECTED";

    public const VERSION            = '1.0';
    public const MINIMUM_VERSION    = '1.0';
    public const SALESFORCE_VERSION = '44.0';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var AbstractClientTransport
     */
    private $transport;

    /**
     * @var AuthProviderInterface
     */
    private $authProvider;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $state = self::DISCONNECTED;

    /**
     * @var ArrayCollection|ChannelInterface[]
     */
    private $channels;

    /**
     * @var string
     */
    private $url;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var array|array<Message[]>
     */
    private $requestQueue = [];

    /**
     * @var ArrayCollection|ExtensionInterface[]
     */
    private $extensions;

    /**
     * BayeuxClient constructor.
     *
     * @param AbstractClientTransport $transport
     * @param AuthProviderInterface $authProvider
     * @param LoggerInterface|null $logger
     *
     * @throws \Exception
     */
    public function __construct(
        AbstractClientTransport $transport,
        AuthProviderInterface $authProvider,
        LoggerInterface $logger = null
    ) {
        $this->transport    = $transport;
        $this->authProvider = $authProvider;
        $this->httpClient   = $this->createClient();
        $this->channels     = new ArrayCollection();
        $this->extensions   = new ArrayCollection();
        $this->logger       = $logger;

        if ($this->transport instanceof HttpClientTransport) {
            $this->transport->setHttpClient($this->httpClient);
        }
    }

    protected function createClient()
    {
        $url = $this->authProvider->getInstanceUrl();

        if (null === $url) {
            $this->authProvider->authorize();
            $url = $this->authProvider->getInstanceUrl();
        }

        return new Client(
            [
                'base_uri' => $url.'/cometd/'.static::SALESFORCE_VERSION.'/',
                'cookies'  => true,
            ]
        );
    }

    /**
     * @param string $channelId
     *
     * @return ChannelInterface
     */
    public function getChannel(string $channelId): ChannelInterface
    {
        if ($this->channels->containsKey($channelId)) {
            return $this->channels->get($channelId);
        }

        $channel = new Channel($channelId);

        // If you wanna listen to meta messages, be my guest but we don't need to subscribe to them
        if ($channel->isMeta()) {
            $this->channels->set($channelId, $channel);

            return $channel;
        }

        $this->getChannel(ChannelInterface::META_SUBSCRIBE)->subscribe(
            Consumer::create(
                function (ChannelInterface $c, Message $message) {
                    if (!$message->isSuccessful()) {
                        if (null !== $this->logger) {
                            $this->logger->error(
                                "Failed to subscribe to channel {channel}",
                                [
                                    'channel' => $c->getChannelId(),
                                ]
                            );
                        }

                        $c->unsubscribeAll();
                        $this->channels->remove($c->getChannelId());
                    }
                }
            )
        )
        ;

        $this->channels->set($channelId, $channel);

        if (null !== $this->clientId) {
            $message = new Message();
            $message->setChannel(ChannelInterface::META_SUBSCRIBE);
            $message->setSubscription($channelId);
            $this->sendMessages([$message]);
        } else {
            // If the handshake hasn't taken place, wait for it and connect in bulk
            $this->getChannel(ChannelInterface::META_HANDSHAKE)->subscribe(
                Consumer::create(
                    function (ChannelInterface $c, Message $m) use ($channel) {
                        if ($m->isSuccessful() && !$channel->isMeta()) {
                            $message = new Message();
                            $message->setChannel(ChannelInterface::META_SUBSCRIBE);
                            $message->setSubscription($channel->getChannelId());

                            $this->sendMessages([$message]);
                        }
                    }
                )
            )
            ;
        }

        return $channel;
    }

    /**
     * Start the Bayeux Client
     *
     * @code <?php
     *      $client = new BayeuxClient(...);
     *      $channel = $client->getChannel('/topic/mytopic');
     *      $channel->subscribe(function(ChannelInterface $c, StreamingData $data) {
     *          ///...
     *      });
     *      $client->start();
     */
    public function start(): void
    {
        if (!$this->isDisconnected()) {
            throw new \RuntimeException("The client must be disconnected before starting.");
        }

        $this->getChannel(ChannelInterface::META_HANDSHAKE)->subscribe(
            Consumer::create(
                function (
                    ChannelInterface $c,
                    Message $message
                ) {
                    if ($message->isSuccessful()) {
                        $this->listen();
                    } else {
                        throw new \RuntimeException("Handshake authentication failed with the server.");
                    }
                },
                1000000
            )
        )
        ;

        $this->handshake();
    }

    public function isDisconnected(): bool
    {
        return in_array(
            $this->state,
            [static::DISCONNECTED, static::DISCONNECTING, static::UNCONNECTED, static::TERMINATING]
        );
    }

    public function handshake(): void
    {
        if (in_array(
            $this->state,
            [
                static::CONNECTING,
                static::CONNECTED,
                static::HANDSHAKING,
                static::HANDSHAKEN,
                static::TERMINATING,
            ]
        )) {
            throw new \RuntimeException("The client must be fully disconnected before handshaking.");
        }

        if ($this->state !== static::REHANDSHAKING) {
            $this->state = static::HANDSHAKING;
        }

        $consumer = Consumer::create(
            function (ChannelInterface $c, Message $message) use (&$consumer) {
                $c->unsubscribe($consumer);

                if ($message->isSuccessful()) {
                    $this->state = static::HANDSHAKEN;

                    return true;
                } else {
                    $advice         = $message->getAdvice();
                    $this->clientId = null;

                    if (null !== $advice && $advice->getReconnect() === 'retry') {
                        $this->state = static::REHANDSHAKING;
                        sleep($advice->getInterval() ?: 0);

                        $this->handshake();
                    } else {
                        $this->state = static::UNCONNECTED;
                    }

                    return false;
                }
            },
            -1000
        );
        $this->getChannel(ChannelInterface::META_HANDSHAKE)->subscribe($consumer);

        $message = new Message();
        $message->setChannel(ChannelInterface::META_HANDSHAKE);
        $message->setSupportedConnectionTypes([$this->transport->getName()]);
        $message->setVersion(static::VERSION);
        $message->setMinimumVersion(static::MINIMUM_VERSION);

        $this->sendMessages([$message]);
    }

    public function connect(): void
    {
        if ($this->state !== static::HANDSHAKEN && $this->state !== static::CONNECTED) {
            throw new \RuntimeException("Cannot connect to the server without first handshaking with it.");
        }

        $this->state = static::CONNECTING;

        $message = new Message();
        $message->setChannel(ChannelInterface::META_CONNECT);
        $message->setConnectionType($this->transport->getName());

        $consumer = Consumer::create(
            function (ChannelInterface $c, Message $message) use (&$consumer) {
                $c->unsubscribe($consumer);

                if ($message->isSuccessful()) {
                    $this->state = static::CONNECTED;
                } else {
                    if (null !== $this->logger) {
                        $this->logger->critical(
                            'Failed to connect with Salesforce: {error}',
                            [
                                'error' => $message->getError(),
                            ]
                        );
                    }

                    $advice = $message->getAdvice();

                    if (null !== $advice && in_array($advice->getReconnect(), ['retry', 'handshake'])) {
                        $interval = $advice->getInterval() ?: 0;
                        sleep($interval);

                        if ($advice->getReconnect() === 'retry') {
                            $this->connect();
                        } else {
                            $this->state = static::REHANDSHAKING;
                            $this->handshake();
                        }
                    } else {
                        $this->disconnect();
                    }
                }
            },
            -1000
        );
        $channel  = $this->getChannel(ChannelInterface::META_CONNECT);
        $channel->subscribe($consumer);

        $this->sendMessages([$message]);
    }

    public function listen(): void
    {
        if ($this->state !== static::HANDSHAKEN) {
            throw new \RuntimeException(
                "A handshake connection with the streaming service must occur before listening."
            );
        }

        $channel = $this->getChannel(ChannelInterface::META_CONNECT);
        $channel->subscribe(
            Consumer::create(
                function (ChannelInterface $c, Message $message) {
                    if ($message->isSuccessful()) {
                        $advice = $message->getAdvice();
                        if (null !== $advice && $advice->getInterval() > 0) {
                            sleep($advice->getInterval());
                        }

                        $this->connect();
                    }
                },
                1000000
            )
        );

        $this->connect();
    }

    public function disconnect(): void
    {
        if (!in_array($this->state, [static::CONNECTING, static::CONNECTED, static::TERMINATING])) {
            throw new \RuntimeException("The server must be connected before disconnecting.");
        }

        if ($this->state !== static::TERMINATING) {
            $this->state = static::DISCONNECTING;
        }

        $unsubscribes = [];

        foreach ($this->channels as $channel) {
            if ($channel->isMeta()) {
                continue;
            }

            $message = new Message();
            $message->setChannel(ChannelInterface::META_UNSUBSCRIBE);
            $message->setSubscription($channel->getChannelId());
            $unsubscribes[] = $message;
        }

        // Unsubscribe channels
        if (count($unsubscribes) > 0) {
            $this->sendMessages($unsubscribes);
        }

        $message = new Message();
        $message->setChannel(ChannelInterface::META_DISCONNECT);

        $this->sendMessages([$message]);
        $this->authProvider->revoke();
        $this->clientId = null;
        // Clear all the channels to make room for new subscriptions
        $this->channels->clear();

        $this->state = static::DISCONNECTED;
    }

    public function terminate(): void
    {
        $this->state = static::TERMINATING;
        $this->transport->terminate();

        $this->disconnect();
    }

    /**
     * @param Message[]|array $messages
     */
    public function sendMessages(array $messages): void
    {
        if (empty($messages)) {
            return;
        }

        if (null !== $this->clientId) {
            foreach ($messages as $message) {
                $message->setClientId($this->clientId);
            }
        }

        $this->prepareExtensions($messages);

        $this->requestQueue[] = $messages;

        $this->processQueue();
    }

    public function prepareExtensions(array $messages)
    {
        foreach ($this->extensions as $extension) {
            foreach ($messages as $message) {
                $extension->prepareSend($message);
            }
        }
    }

    public function processExtensions(array $messages): void
    {
        foreach ($this->extensions as $extension) {
            foreach ($messages as $message) {
                $extension->processReceive($message);
            }
        }
    }

    public function processQueue(): void
    {
        $messages = array_shift($this->requestQueue);

        if (null === $messages) {
            return;
        }

        try {
            $newMessages = $this->transport->send(
                $messages,
                function (RequestInterface $request) {
                    $this->authProvider->authorize();
                    return $request->withAddedHeader('Authorization', 'OAuth '. $this->authProvider->getToken());
                }
            );
        } catch (SessionExpiredOrInvalidException $e) {
            array_unshift($this->requestQueue, $messages);
            $this->authProvider->reauthorize();
            $this->processQueue();

            return;
        }

        $this->processExtensions($newMessages);

        foreach ($newMessages as $message) {
            $channel = $this->getChannel($message->getChannel());

            if (null !== $message->getClientId()) {
                $this->clientId = $message->getClientId();
            }

            if (null !== $channel) {
                $channel->notifyMessageListeners($message);
            }
        }

        $this->processQueue();
    }

    /**
     * @return AbstractClientTransport
     */
    public function getTransport(): AbstractClientTransport
    {
        return $this->transport;
    }

    /**
     * @param AbstractClientTransport $transport
     *
     * @return BayeuxClient
     */
    public function setTransport(AbstractClientTransport $transport): BayeuxClient
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * @return AuthProviderInterface
     */
    public function getAuthProvider(): AuthProviderInterface
    {
        return $this->authProvider;
    }

    /**
     * @param AuthProviderInterface $authProvider
     *
     * @return BayeuxClient
     */
    public function setAuthProvider(AuthProviderInterface $authProvider): BayeuxClient
    {
        $this->authProvider = $authProvider;

        return $this;
    }

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return ArrayCollection
     */
    public function getChannels(): ArrayCollection
    {
        return $this->channels;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param ExtensionInterface $extension
     *
     * @return BayeuxClient
     */
    public function addExtension(ExtensionInterface $extension): BayeuxClient
    {
        if (!$this->extensions->contains($extension)) {
            $this->extensions->add($extension);
        }

        return $this;
    }
}
