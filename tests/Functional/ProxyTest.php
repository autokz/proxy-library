<?php

declare(strict_types=1);


namespace Test\Functional;

use Exception;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;

class ProxyTest extends WebTestCase
{
    public function testLoginMethodSuccess(): void
    {
        $jwt = $this->converter->fromFrontendToJWT($this->login());

        $this->assertCorrectJwt($jwt);
    }

    public function testLoginMethodEmptyData(): void
    {
        $username = '__INCORRECT-USERNAME__';
        $password = '__INCORRECT-PASSWORD__';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The User Entity not found, check user, domain credentials.');
        $this->expectExceptionCode(400);

        $this->login($username, $password);
    }

    public function testCheckMethodSuccess(): void
    {
        $cryptedAccessToken = $this->getJwt('access_token');

        $OAuthDataFromCheck = $this->proxy->check($cryptedAccessToken);

        $jwt = $this->converter->fromFrontendToJWT($OAuthDataFromCheck);

        self::assertArrayHasKey('access_token', $jwt);
    }

    public function testCheckMethodInvalid(): void
    {
        $cryptedAccessToken = $this->getJwt('access_token', true);

        $cryptedAccessToken['access_token'] .= '__INVALID__';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The resource owner or authorization server denied the request.');
        $this->expectExceptionCode(400);

        $this->proxy->check($this->converter->fromJWTToFrontend($cryptedAccessToken));
    }

    public function testRefreshMethodSuccess(): void
    {
        $cryptedAccessToken = $this->getJwt('refresh_token');

        $jwt = $this->proxy->refresh($cryptedAccessToken);

        $this->assertCorrectJwt($this->converter->fromFrontendToJWT($jwt));
    }

    public function testRefreshMethodInvalid(): void
    {
        $cryptedAccessToken = $this->getJwt('refresh_token', true);
        $cryptedAccessToken['refresh_token'] .= '__INVALID__';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The refresh token is invalid.');
        $this->expectExceptionCode(400);

        $this->proxy->refresh($this->converter->fromJWTToFrontend($cryptedAccessToken));
    }

    public function testLogoutMethodSuccess(): void
    {
        $OAuthData = $this->getJwt('access_token');

        $result = $this->proxy->logout($OAuthData);

        self::AssertTrue($result);
    }

    public function testLogoutMethodInvalid(): void
    {
        $cryptedAccessToken = $this->getJwt('access_token', true);
        $cryptedAccessToken['access_token'] .= '__INVALID__';

        $cryptedAccessToken = $this->converter->fromJWTToFrontend($cryptedAccessToken);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The resource owner or authorization server denied the request.');
        $this->expectExceptionCode(400);

        $this->proxy->logout($cryptedAccessToken);
    }

    private function getJwt(string $nameToken, ?bool $assoc = false)
    {
        $OAuthData = $this->converter->fromFrontendToJWT($this->login());

        $accessToken = array_filter(
            $OAuthData,
            function ($key) use ($nameToken) {
                return $key === $nameToken;
            },
            ARRAY_FILTER_USE_KEY
        );

        if ($assoc) {
            return $accessToken;
        }
        return $this->converter->fromJWTToFrontend($accessToken);
    }

    private function login(string $login = 'farid@auto.kz', string $password = 'fred777a'): string
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