<?php

declare(strict_types=1);


namespace Test\Functional;

use Exception;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;

class ProxyTest extends WebTestCase
{
    public function testLoginSuccess(): void
    {
        $jwt = $this->converter->fromFrontendToJWT($this->login());

        $this->assertCorrectJwt($jwt);
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
        $OAuthData = $this->login();

        $OAuthDataFromCheck = $this->proxy->check($OAuthData);

        $jwt = $this->converter->fromFrontendToJWT($OAuthDataFromCheck);

        $this->assertCorrectJwt($jwt);
    }

    public function testCheckInvalid(): void
    {
        $result = [
            "token_type" => "__INCORRECT__TYPE__",
            "expires_in" => "__INCORRECT__EXPIRES__",
            "access_token" => "__INCORRECT__ACCESS-TOKEN__",
            "refresh_token" => "__INCORRECT__REFRESH-TOKEN__"
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The refresh token is invalid.');
        $this->expectExceptionCode(400);

        $this->proxy->check($this->converter->fromJWTToFrontend($result));
    }

    private function login(?string $login = 'login', ?string $password = 'password'): string
    {
        return $this->proxy->login(new UsernameType($login), new PasswordType($password));
    }

    public function assertCorrectJwt($jwt): void
    {
        self::assertTrue(is_array($jwt));
        self::assertArrayHasKey('token_type', $jwt);
        self::assertArrayHasKey('expires_in', $jwt);
        self::assertArrayHasKey('access_token', $jwt);
        self::assertArrayHasKey('refresh_token', $jwt);
    }
}