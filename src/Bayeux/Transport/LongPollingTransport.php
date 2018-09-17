<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 12:19 PM
 */

namespace AE\SalesforceRestSdk\Bayeux\Transport;

use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Message;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\SerializerInterface;
use GuzzleHttp\Promise\PromiseInterface;

class LongPollingTransport extends HttpClientTransport
{
    /**
     * @var bool
     */
    private $aborted = false;

    public function __construct()
    {
        parent::__construct('long-polling');
    }

    public function abort()
    {
        $this->aborted = true;
    }

    /**
     * @return bool
     */
    public function isAborted(): bool
    {
        return $this->aborted;
    }

    /**
     * @param Message[]|array $messages
     * @param callable|null $customize
     *
     * @throws GuzzleException
     *
     * @return array
     */
    public function send($messages, ?callable $customize = null): array
    {
        $this->aborted = false;
        $client        = $this->getHttpClient();
        $url           = $this->getUrl() ?: '';

        if (count($messages) == 1 && $messages[0]->isMeta()) {
            $type = substr($messages[0]->getChannel(), strlen(ChannelInterface::META));
            $url  .= 'meta'.$type;
        }

        if (substr($url, 0, 1) == '/') {
            $url .= substr($url, 1, strlen($url));
        }

        $request = new Request(
            "POST",
            $url,
            [
                'User-Agent'   => 'ae-connect-bayeux-client/1.0',
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
            ],
            $this->generateJSON($messages)
        );

        if (null !== $customize) {
            $request = call_user_func($customize, $request);
        }

        $response = $client->send($request);
        $body     = (string)$response->getBody();

        if (strlen($body) > 0) {
            /** @var Message[] $messages */
            $messages = $this->parseMessages($body);

            // Need to reorder the messages that will be dispatched in the order they were dispatched, but connect
            // needs to be last, otherwise the messages won't process
            if (count($messages) > 0 &&
                $messages[count($messages) - 1]->getClientId() === ChannelInterface::META_CONNECT) {
                $reconnect  = array_pop($messages);
                $messages   = array_reverse($messages);
                $messages[] = $reconnect;
            }

            return $messages;
        }

        return [];
    }

    public function terminate()
    {
        $this->abort();
    }
}
