<?php
declare(strict_types=1);


namespace Test\Functional;

use Exception;
use Proxy\OAuth\Action\AccessAction;
use Test\Builder\JwtConverterBuilder;

class AccessActionTest extends WebTestCase
{
    public function testSuccess(): void
    {
        $oauthDataFromLogin = $this->login();

        $accessAction = new AccessAction($this->converter, $this->configStore);
        $result = $accessAction->execute($oauthDataFromLogin);

        $convertedResult = $this->converter->fromFrontendToJWT($result);

        self::assertTrue(is_array($convertedResult));
        self::assertArrayHasKey('token_type', $convertedResult);
        self::assertArrayHasKey('expires_in', $convertedResult);
        self::assertArrayHasKey('access_token', $convertedResult);
        self::assertArrayHasKey('refresh_token', $convertedResult);
        self::assertEquals('BearerTest', $convertedResult['token_type']);
    }

    public function testInvalid(): void
    {
        $result = [
            "token_type" => 'Bearer',
            "expires_in" => "60",
            "access_token" => "asdf",
            "refresh_token" => "ASDF"
        ];

        $accessAction = new AccessAction($this->converter, $this->configStore);

        self::assertTrue(is_array($result));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The refresh token is invalid.');
        $this->expectExceptionCode(400);

        $convertedResult = $this->converter->fromJWTToFrontend($result);

        $accessAction->execute($convertedResult);
    }
}