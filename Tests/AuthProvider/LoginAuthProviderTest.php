<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/27/18
 * Time: 6:02 PM
 */

namespace AE\SalesforceRestSdk\Tests\AuthProvider;

use AE\SalesforceRestSdk\AuthProvider\LoginProvider;
use PHPUnit\Framework\TestCase;

class LoginAuthProviderTest extends TestCase
{
    public function testReauthorize()
    {
        $auth = new LoginProvider(
            getenv("SF_CLIENT_ID"),
            getenv("SF_CLIENT_SECRET"),
            getenv("SF_USER"),
            getenv("SF_PASS"),
            getenv("SF_LOGIN_URL")
        );

        $class = new \ReflectionClass(LoginProvider::class);

        $tokenProperty = $class->getProperty('token');
        $tokenProperty->setAccessible(true);
        $tokenProperty->setValue($auth, 'BAD_VALUE');

        $header = $auth->reauthorize();

        $this->assertNotNull($header);
        $this->assertNotNull($auth->getToken());
        $this->assertEquals('Bearer', $auth->getTokenType());
    }
}
