<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 2:31 PM
 */

namespace AE\SalesforceRestSdk\Tests\Rest;

use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;
use AE\SalesforceRestSdk\Model\Rest\GenericEvent;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\GenericEventClient;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class GenericEventClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $sObjectClient;

    /**
     * @var SObject
     */
    private $channel;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $restClient    = new \AE\SalesforceRestSdk\Rest\Client(
            new OAuthProvider(
                getenv("SF_CLIENT_ID"),
                getenv("SF_CLIENT_SECRET"),
                getenv("SF_LOGIN_URL"),
                getenv("SF_USER"),
                getenv("SF_PASS")
            ),
            "45.0"
        );
        $this->sObjectClient = $restClient->getSObjectClient();

        $this->channel = new SObject(
            [
                'name' => '/u/test/TestChannel',
            ]
        );

        $this->sObjectClient->persist('StreamingChannel', $this->channel);
    }

    public function testGenericEvents()
    {

        $adapter       = new ArrayAdapter();
        $client        = new GenericEventClient($adapter, $this->sObjectClient);

        $this->assertEquals($this->channel->Id, $client->getStreamingChannelId('/u/test/TestChannel'));
        $cache = $adapter->getItem('GENERIC_EVENTS')->get();
        $this->assertEquals($this->channel->Id, $cache['/u/test/TestChannel']);

        $event = new GenericEvent();
        $event->setPayload("Testing the Event");

        $res = $client->sendEvent('/u/test/TestChannel', $event);
        $this->assertNotNull($res);
        $this->assertGreaterThanOrEqual(0, $res->getFanoutCount());
    }

    protected function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->sObjectClient->remove('StreamingChannel', $this->channel);
    }
}
