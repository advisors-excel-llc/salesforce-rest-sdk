<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 3:38 PM
 */
namespace AE\SalesforceRestSdk\Tests\Rest;

use AE\SalesforceRestSdk\AuthProvider\LoginProvider;
use AE\SalesforceRestSdk\Rest\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->client = new Client(
            new LoginProvider(
                getenv("SF_CLIENT_ID"),
                getenv("SF_CLIENT_SECRET"),
                getenv("SF_USER"),
                getenv("SF_PASS"),
                getenv("SF_LOGIN_URL")
            )
        );
    }

    public function testLimits()
    {
        $limits = $this->client->limits();

        $this->assertNotNull($limits);
        $this->greaterThanOrEqual(0)->evaluate($limits->getDailyApiRequests()->getMax());
        $this->greaterThanOrEqual(0)->evaluate($limits->getDailyApiRequests()->getRemaining());
    }

    public function testRetry()
    {
        $limits = $this->client->limits();

        $this->assertNotNull($limits);

        $class = new \ReflectionClass(LoginProvider::class);

        $tokenProperty = $class->getProperty('token');
        $tokenProperty->setAccessible(true);
        $tokenProperty->setValue($this->client->getAuthProvider(), 'BAD_VALUE');

        $this->assertEquals('BAD_VALUE', $this->client->getAuthProvider()->getToken());
        $limits = $this->client->limits();

        $this->assertNotNull($limits);
    }
}
