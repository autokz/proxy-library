<?php


namespace Test\Functional;

use Exception;
use Proxy\OAuth\Action\LogoutAction;
use Test\Builder\JwtConverterBuilder;

class LogoutActionTest extends WebTestCase
{

    public function testSuccess(): void
    {
        $logoutAction = new LogoutAction($this->converter, $this->configStore, $this->httpClient);

        $token = $this->login();
        $result = $logoutAction->execute($token);

        self::AssertTrue($result);
    }

    public function testIncorrectRefresh(): void
    {
        $logoutAction = new LogoutAction($this->converter, $this->configStore, $this->httpClient);

        $token = $this->login();

        $convertedResult =  $this->converter->fromFrontendToJWT($token);

        $convertedResult['access_token'] .= '__INCORRECT___';
        $convertedResult['refresh_token'] .= '__INCORRECT___';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The refresh token is invalid.');
        $this->expectExceptionCode(400);

        $convertedResult =  $this->converter->fromJWTToFrontend($convertedResult);
        $logoutAction->execute($convertedResult);
    }

}