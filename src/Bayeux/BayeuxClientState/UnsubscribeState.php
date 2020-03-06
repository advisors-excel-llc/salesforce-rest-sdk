<?php

namespace AE\SalesforceRestSdk\Bayeux\BayeuxClientState;

use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Message;

class UnsubscribeState extends ClientState
{

    public function init()
    {
        return;
    }

    public function handle()
    {
        $unsubscribes = [];

        foreach ($this->getContext()->getChannels() as $channel) {
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
            $this->getContext()->sendMessages($unsubscribes);
        }
    }
}
