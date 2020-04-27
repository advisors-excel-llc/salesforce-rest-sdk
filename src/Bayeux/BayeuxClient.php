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
use AE\SalesforceRestSdk\Bayeux\BayeuxClientState as State;
use AE\SalesforceRestSdk\Bayeux\Extension\ExtensionInterface;
use AE\SalesforceRestSdk\Bayeux\Transport\AbstractClientTransport;
use AE\SalesforceRestSdk\Bayeux\Transport\HttpClientTransport;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * @var State\ClientState
     */
    private $state;

    /** @var array|string[] */
    private $previousStates = [];

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
     * @param string $version
     *
     * @throws \Exception
     */
    public function __construct(
        AbstractClientTransport $transport,
        AuthProviderInterface $authProvider,
        LoggerInterface $logger = null,
        string $version = "44.0"
    ) {
        $this->transport    = $transport;
        $this->authProvider = $authProvider;
        $this->httpClient   = $this->createClient($version);
        $this->channels     = new ArrayCollection();
        $this->extensions   = new ArrayCollection();
        $this->logger       = $logger ?: new NullLogger();

        if ($this->transport instanceof HttpClientTransport) {
            $this->transport->setHttpClient($this->httpClient);
        }
    }

    protected function createClient(string $version = "44.0")
    {
        $url = $this->authProvider->getInstanceUrl();

        if (null === $url) {
            $this->authProvider->authorize();
            $url = $this->authProvider->getInstanceUrl();
        }

        return new Client(
            [
                'base_uri' => $url.'/cometd/'.$version.'/',
                'cookies'  => true,
            ]
        );
    }

    public function start()
    {
        $this->previousStates = [];
        $this->setState(new State\HandshakeState($this, $this->logger));
        do {
            $this->state->handle();
        } while (!$this->state instanceof State\DisconnectState);
    }

    public function setState(State\ClientState $state)
    {
        if (array_search(get_class($state), $this->previousStates) === false) {
            $state->init();
            $this->previousStates[] = get_class($state);
        }
        $this->state = $state;
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

        $this->channels->set($channelId, $channel);

        return $channel;
    }

    public function isDisconnected(): bool
    {
        return $this->state instanceof State\DisconnectState;
    }

    public function handshake(): void
    {
        $this->setState(new State\HandshakeState($this, $this->logger));
        $this->state->handle();
    }

    public function connect(): void
    {
        $this->setState(new State\ConnectState($this, $this->logger));
        $this->state->handle();
    }

    /**
     * @deprecated use connect instead.
     */
    public function listen(): void
    {
        $this->connect();
    }

    public function disconnect(): void
    {
        $this->setState(new State\DisconnectState($this, $this->logger));
        $this->state->handle();
    }

    public function terminate(): void
    {
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
        /** @var Message[] $messages */
        $messages = array_shift($this->requestQueue);

        if (null === $messages) {
            return;
        }

        foreach ($messages as $message) {
            $this->getChannel($message->getChannel())->prepareOutgoingMessage($message);
            if ($message->getSubscription() && isset($this->channels[$message->getSubscription()])) {
                $this->getChannel($message->getSubscription())->prepareOutgoingMessage($message);
            }
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
            $this->logger->notice("ERROR OCCURRED: ".$e->getMessage());
            $this->logger->info("Attempting to reauthenticate");
            $this->authProvider->reauthorize();
            $this->logger->info("Reauthentication successful");
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

    public function clearClientId()
    {
        $this->clientId = null;
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
