<?php

declare(strict_types=1);


namespace Test\Functional;


use Exception;
use Proxy\OAuth\Action\LoginAction;
use Proxy\OAuth\Action\Type\PasswordType;
use Proxy\OAuth\Action\Type\UsernameType;
use Test\Builder\JwtConverterBuilder;

class LoginActionTest extends WebTestCase
{
    public function testSuccess(): void
    {
        $result = $this->login();

        $convertedResult = $this->converter->fromFrontendToJWT($result);

        self::assertTrue(is_array($result));
        self::assertArrayHasKey('token_type', $convertedResult);
        self::assertArrayHasKey('expires_in', $convertedResult);
        self::assertArrayHasKey('access_token', $convertedResult);
        self::assertArrayHasKey('refresh_token', $convertedResult);
        self::assertEquals('BearerTest', $convertedResult['token_type']);
    }

    public function testInvalid(): void
    {
        $authAction = new LoginAction($this->converter, $this->configStore, $this->httpClient);

        $username = new UsernameType('user');
        $password = new PasswordType('password');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The User Entity not found, check user, domain credentials.');
        $this->expectExceptionCode(400);

        $authAction->login($username, $password);
    }
}