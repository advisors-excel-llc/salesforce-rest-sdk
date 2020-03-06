<?php

namespace AE\SalesforceRestSdk\Bayeux\BayeuxClientState;

use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Consumer;
use AE\SalesforceRestSdk\Bayeux\Message;

class SubscribeState extends ClientState
{
    public function init()
    {
        $this->getContext()->getChannel(ChannelInterface::META_SUBSCRIBE)->subscribe(
            Consumer::create(
                function (ChannelInterface $c, Message $message) {
                    if (!$message->isSuccessful()) {
                        $this->logger->error(
                            "Failed to subscribe to channel {channel}",
                            [
                                'channel' => $c->getChannelId(),
                            ]
                        );
                    }
                }
            )
        )
        ;
    }

    public function handle()
    {
        foreach ($this->getContext()->getChannels() as $channel) {
            /** @var ChannelInterface $channel */
            if ($channel->isMeta()) {
                continue;
            }
            $message = new Message();
            $message->setChannel(ChannelInterface::META_SUBSCRIBE);
            $message->setSubscription($channel->getChannelId());
            $this->getContext()->sendMessages([$message]);
        }
        $this->transitionToState(ConnectState::class);
    }
}
