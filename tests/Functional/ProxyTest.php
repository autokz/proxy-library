<?php

declare(strict_types=1);


namespace Test\Functional;

use Exception;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;
use Proxy\OAuth\Proxy;

class ProxyTest extends WebTestCase
{

    public function testLoginSuccess(): void
    {
        $jwt = $this->converter->fromFrontendToJWT($this->login());

        self::assertTrue(is_array($jwt));
        self::assertArrayHasKey('token_type', $jwt);
        self::assertArrayHasKey('expires_in', $jwt);
        self::assertArrayHasKey('access_token', $jwt);
        self::assertArrayHasKey('refresh_token', $jwt);
    }

    public function testLoginEmptyData(): void
    {
        $username = '__INCORRECT__USERNAME__';
        $password = '__INCORRECT__PASSWORD__';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The User Entity not found, check user, domain credentials.');
        $this->expectExceptionCode(400);

        $this->login($username, $password);
    }

    public function testCheckSuccess(): void
    {
    }

    private function login(?string $login = 'login', ?string $password = 'password'): string
    {
        $authAction = new Proxy($this->converter, $this->configStore);

        return $authAction->login(new UsernameType($login), new PasswordType($password));
    }
}