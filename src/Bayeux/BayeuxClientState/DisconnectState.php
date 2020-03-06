<?php

namespace AE\SalesforceRestSdk\Bayeux\BayeuxClientState;

use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Message;

class DisconnectState extends ClientState
{
    public function init()
    {
        return;
    }

    public function handle()
    {
        //We wanna unsubscribe any time we disconnect too.
        $unsubscribe = new UnsubscribeState($this->getContext(), $this->logger);
        $unsubscribe->handle();

        $message = new Message();
        $message->setChannel(ChannelInterface::META_DISCONNECT);

        $this->getContext()->sendMessages([$message]);
        $this->getContext()->getAuthProvider()->revoke();
        $this->getContext()->clearClientId();
        // Clear all the channels to make room for new subscriptions
        $this->getContext()->getChannels()->clear();
    }
}
