<?php

declare(strict_types=1);


namespace Test\Functional;

use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;
use Proxy\OAuth\Proxy;

class ProxyTest extends WebTestCase
{

    public function testLoginSuccess(): void
    {
        $username = 'login';
        $password = 'login';

        $jwt = $this->converter->fromFrontendToJWT($this->login($username, $password));

        self::assertTrue(is_array($jwt));
        self::assertArrayHasKey('token_type', $jwt);
        self::assertArrayHasKey('expires_in', $jwt);
        self::assertArrayHasKey('access_token', $jwt);
        self::assertArrayHasKey('refresh_token', $jwt);
    }

    private function login(string $login, string $password): string
    {
        $authAction = new Proxy($this->converter, $this->configStore);

        return $authAction->login(new UsernameType($login), new PasswordType($password));
    }
}