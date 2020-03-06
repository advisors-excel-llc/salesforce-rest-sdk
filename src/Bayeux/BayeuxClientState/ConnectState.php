<?php

namespace AE\SalesforceRestSdk\Bayeux\BayeuxClientState;

use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Consumer;
use AE\SalesforceRestSdk\Bayeux\Message;

class ConnectState extends ClientState
{
    public function init()
    {
        $this->getContext()->getChannel(ChannelInterface::META_CONNECT)->subscribe(
            Consumer::create(
                function (ChannelInterface $c, Message $message) {

                    $advice = $message->getAdvice();

                    if (null !== $advice && $advice->getInterval() > 0) {
                        sleep($advice->getInterval());
                    }

                    if (!$message->isSuccessful()) {
                        $this->logger->critical(
                            'Failed to connect with Salesforce: {error}',
                            [
                                'error' => $message->getError(),
                            ]
                        );

                        if (null !== $advice && in_array($advice->getReconnect(), ['retry', 'handshake'])) {

                            if ($advice->getReconnect() === 'retry') {
                                //SF wants us to retry, so we just stay in this state
                                return;
                            } else {
                                //We got banished to HANDSHAKING again, lets transition
                                $this->transitionToState(HandshakeState::class);
                            }
                        } else {
                            // We got owned, give up and disconnect
                            $this->transitionToState(DisconnectState::class);
                        }
                    }
                    //Any time we successfully connect we will want to just connect again, so no need to transition states here either.
                },
            -1000
            )
        );
    }

    public function handle()
    {
        $message = new Message();
        $message->setChannel(ChannelInterface::META_CONNECT);
        $message->setConnectionType($this->getContext()->getTransport()->getName());
        $this->getContext()->sendMessages([$message]);
    }
}
