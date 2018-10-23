<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/27/18
 * Time: 6:02 PM
 */

namespace AE\SalesforceRestSdk\Tests\AuthProvider;

use AE\SalesforceRestSdk\AuthProvider\SoapProvider;
use PHPUnit\Framework\TestCase;

class SoapAuthProviderTest extends TestCase
{
    public function testReauthorize()
    {
        $auth = new SoapProvider(
            getenv("SF_USER"),
            getenv("SF_PASS"),
            getenv("SF_LOGIN_URL")
        );

        $header = $auth->authorize();

        $this->assertNotNull($header);
        $this->assertNotNull($auth->getToken());
        $this->assertNotNull($auth->getInstanceUrl());

        $class = new \ReflectionClass(SoapProvider::class);

        $tokenProperty = $class->getProperty('token');
        $tokenProperty->setAccessible(true);
        $tokenProperty->setValue($auth, 'BAD_VALUE');

        $header = $auth->reauthorize();

        $this->assertNotNull($header);
        $this->assertNotNull($auth->getToken());
        $this->assertNotNull($auth->getInstanceUrl());
        $this->assertEquals('Bearer', $auth->getTokenType());
    }
}
