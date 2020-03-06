<?php

namespace  AE\SalesforceRestSdk\Bayeux\BayeuxClientState;

use AE\SalesforceRestSdk\Bayeux\BayeuxClient;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;

abstract class ClientState
{
    /** @var BayeuxClient */
    private $context;
    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    public abstract function init();
    public abstract function handle();

    public function __construct(BayeuxClient $context, LoggerInterface $logger)
    {
        $this->context = $context;
        $this->logger = $logger;
    }

    public function getContext(): BayeuxClient
    {
        return $this->context;
    }

    /**
     * @param $nextState
     * @return mixed
     */
    public function transitionToState(string $nextState) : bool
    {
        switch ($nextState) {
            case HandshakeState::class:
            case SubscribeState::class:
            case UnsubscribeState::class:
            case ConnectState::class:
            case DisconnectState::class:
                $this->getContext()->setState(new $nextState($this->context, $this->logger));
                return true;
        }
        throw new InvalidArgumentException('Class ' . $nextState . ' is not a valid state.');
    }
}
