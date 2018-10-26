<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/10/18
 * Time: 10:27 AM
 */

namespace AE\SalesforceRestSdk\Tests\Bayeux;

use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;
use AE\SalesforceRestSdk\AuthProvider\SoapProvider;
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
            new LongPollingTransport(),
            new OAuthProvider(
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
                $client   = new Client(['base_uri' => $this->client->getAuthProvider()->getInstanceUrl()]);
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
                    $this->assertNotNull($sobject->Id);
                    $this->assertEquals($name, $sobject->Name);

                    if (!$this->client->isDisconnected()) {
                        $this->client->disconnect();
                    }

                    $client   = new Client(['base_uri' => $this->client->getAuthProvider()->getInstanceUrl()]);
                    $response = $client->delete(
                        'services/data/v43.0/sobjects/Account/'.$sobject->Id,
                        [
                            'headers' => [
                                'Content-Type'  => 'application/json',
                                'Accept'        => 'application/json',
                                'Authorization' => $this->client->getAuthProvider()->authorize(),
                            ],
                        ]
                    );

                    $this->assertEquals(204, $response->getStatusCode());
                }
            )
        );

        $this->client->start();
    }

    public function testHandshakeReauth()
    {
        if (!$this->client->isDisconnected()) {
            $this->client->disconnect();
        }

        $class = new \ReflectionClass(OAuthProvider::class);

        $tokenProperty = $class->getProperty('token');
        $tokenProperty->setAccessible(true);
        $tokenProperty->setValue($this->client->getAuthProvider(), 'BAD_VALUE');

        $typeProperty = $class->getProperty('tokenType');
        $typeProperty->setAccessible(true);
        $typeProperty->setValue($this->client->getAuthProvider(), 'Bearer');

        $authProperty = $class->getProperty('isAuthorized');
        $authProperty->setAccessible(true);
        $authProperty->setValue($this->client->getAuthProvider(), true);

        $this->assertNotNull($this->client->getAuthProvider()->getToken());
        $this->assertTrue($this->client->getAuthProvider()->isAuthorized());

        $this->client->handshake();

        $this->assertNotNull($this->client->getAuthProvider()->getToken());
        $this->assertNotEquals('BAD_VALUE', $this->client->getAuthProvider()->getToken());
        $this->assertTrue($this->client->getAuthProvider()->isAuthorized());

        if (!$this->client->isDisconnected()) {
            $this->client->terminate();
        }
    }
}
