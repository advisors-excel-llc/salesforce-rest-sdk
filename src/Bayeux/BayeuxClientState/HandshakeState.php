<?php

namespace AE\SalesforceRestSdk\Bayeux\BayeuxClientState;

use AE\SalesforceRestSdk\Bayeux\BayeuxClient;
use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Consumer;
use AE\SalesforceRestSdk\Bayeux\Message;

class HandshakeState extends ClientState
{
    public function init()
    {
        $this->getContext()->getChannel(ChannelInterface::META_HANDSHAKE)->subscribe(
            Consumer::create(
                function (
                    ChannelInterface $c,
                    Message $message
                ) {
                    if ($message->isSuccessful()) {
                        // If we successfully handshake, we will transition to the Subscribe state.
                        $this->transitionToState(SubscribeState::class);
                    } else {
                        $this->logger->critical("Handshake authentication failed with the server.");
                        $advice = $message->getAdvice();
                        $this->getContext()->clearClientId();
                        if (null !== $advice && $advice->getReconnect() === 'retry') {
                            sleep($advice->getInterval() ?: 0);
                            // We won't make any state transitions so the Handshake step will re run once we exit
                            return;
                        }
                        // lol we dead
                        $this->transitionToState(DisconnectState::class);
                    }
                },
                1000000
            )
        )
        ;
    }

    public function handle()
    {
        $message = new Message();
        $message->setChannel(ChannelInterface::META_HANDSHAKE);
        $message->setSupportedConnectionTypes([$this->getContext()->getTransport()->getName()]);
        $message->setVersion(BayeuxClient::VERSION);
        $message->setMinimumVersion(BayeuxClient::MINIMUM_VERSION);

        $this->getContext()->sendMessages([$message]);
    }
}
