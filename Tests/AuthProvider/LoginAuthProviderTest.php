<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/27/18
 * Time: 6:02 PM
 */

namespace AE\SalesforceRestSdk\Tests\AuthProvider;

use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;
use PHPUnit\Framework\TestCase;

class LoginAuthProviderTest extends TestCase
{
    public function testReauthorize()
    {
        $auth = new OAuthProvider(
            getenv("SF_CLIENT_ID"),
            getenv("SF_CLIENT_SECRET"),
            getenv("SF_LOGIN_URL"),
            getenv("SF_USER"),
            getenv("SF_PASS")
        );

        $class = new \ReflectionClass(OAuthProvider::class);

        $tokenProperty = $class->getProperty('token');
        $tokenProperty->setAccessible(true);
        $tokenProperty->setValue($auth, 'BAD_VALUE');

        $header = $auth->reauthorize();

        $this->assertNotNull($header);
        $this->assertNotNull($auth->getToken());
        $this->assertEquals('Bearer', $auth->getTokenType());
    }

    public function testRefreshToken()
    {
        $auth = new OAuthProvider(
            getenv("SF_CLIENT_ID"),
            getenv("SF_CLIENT_SECRET"),
            getenv("SF_LOGIN_URL"),
            null,
            null,
            OAuthProvider::GRANT_CODE,
            getenv('SF_REDIRECT_URI'),
            getenv('SF_AUTH_CODE')
        );

        $auth->authorize();

        $this->assertTrue($auth->isAuthorized());
        $this->assertNotNull($auth->getUsername());
        $this->assertNotNull($auth->getRefreshToken());

        // Set the code to something it can't use so we're sure it's using the refresh token
        $auth->setCode(null);
        $auth->reauthorize();

        $this->assertTrue($auth->isAuthorized());
        $this->assertNotNull($auth->getUsername());
        $this->assertNotNull($auth->getToken());
        $this->assertNotNull($auth->getRefreshToken());
    }
}
