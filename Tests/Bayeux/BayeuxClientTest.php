<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/10/18
 * Time: 10:27 AM
 */

namespace AE\SalesforceRestSdk\Tests\Bayeux;

use AE\SalesforceRestSdk\AuthProvider\LoginProvider;
use AE\SalesforceRestSdk\Bayeux\BayeuxClient;
use AE\SalesforceRestSdk\Bayeux\ChannelInterface;
use AE\SalesforceRestSdk\Bayeux\Consumer;
use AE\SalesforceRestSdk\Bayeux\Message;
use AE\SalesforceRestSdk\Bayeux\Transport\LongPollingTransport;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class BayeuxClientTest extends TestCase
{
    /**
     * @var BayeuxClient
     */
    private $client;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        $this->client = new BayeuxClient(
            getenv("SF_URL"),
            new LongPollingTransport(),
            new LoginProvider(
                getenv("SF_CLIENT_ID"),
                getenv("SF_CLIENT_SECRET"),
                getenv("SF_USER"),
                getenv("SF_PASS"),
                getenv("SF_LOGIN_URL")
            )
        );
    }

    public function testStream()
    {
        $rand = rand(100, 1000);
        $name = 'Test Account '.$rand;

        $consumer = Consumer::create(
            function (ChannelInterface $channel, Message $message) use ($name, &$consumer) {
                $this->assertTrue($message->isSuccessful());
                $this->assertFalse($this->client->isDisconnected());
                $client   = new Client(['base_uri' => getenv('SF_URL')]);
                $response = $client->post(
                    'services/data/v43.0/sobjects/Account',
                    [
                        'headers' => [
                            'Content-Type'  => 'application/json',
                            'Accept'        => 'application/json',
                            'Authorization' => $this->client->getAuthProvider()->authorize(),
                        ],
                        'json'    => ['Name' => $name],
                    ]
                );

                $this->assertEquals(201, $response->getStatusCode());
                $channel->unsubscribe($consumer);
            }
        );

        $this->client->getChannel(ChannelInterface::META_CONNECT)->subscribe($consumer);

        $channel = $this->client->getChannel('/topic/Accounts');
        $channel->subscribe(
            Consumer::create(
                function (ChannelInterface $channel, Message $message) use ($name) {
                    $this->assertEquals("/topic/Accounts", $channel->getChannelId());
                    $data = $message->getData();
                    $this->assertNotNull($data);
                    $sobject = $data->getSobject();
                    $this->assertNotNull($sobject);
                    $this->assertEquals($name, $sobject->Name);

                    if (!$this->client->isDisconnected()) {
                        $this->client->disconnect();
                    }
                }
            )
        );

        $this->client->start();
    }
}
