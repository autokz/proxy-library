<?php

declare(strict_types=1);

namespace Proxy\OAuth;

use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Command\Logout\Handler;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;
use Proxy\OAuth\ReadModel\Access\JwtFetcher;

class Proxy
{
    private ConverterInterface $converter;
    private ConfigStorageInterface $configStore;
    private HttpClientInterface $httpClient;

    private JwtFetcher $fetcher;

    public function __construct(
        ConverterInterface $converter,
        ConfigStorageInterface $configStore,
        HttpClientInterface $httpClient = null
    ) {
        $this->converter = $converter;

        $configStore->load();
        $this->configStore = $configStore;

        $this->httpClient = $httpClient ?? new GuzzleHttpClient();

        $this->fetcher = new JwtFetcher($this->configStore, $this->httpClient);
    }

    public function login(UsernameType $username, PasswordType $password): string
    {
        $jwt = $this->fetcher->getJwtByUsernamePassword($username, $password);

        return $this->converter->fromJWTToFrontend($jwt);
    }

    public function logout(string $authData): bool
    {
        $jwt = $this->converter->fromFrontendToJWT($authData);

        $logoutHandler = new Handler($this->converter, $this->configStore, $this->httpClient);
        $logoutHandler->handle($jwt);

        $this->converter->fromJWTToFrontend([]);

        return true;
    }

    public function check(string $authData): string
    {
        $accessToken = $this->converter->fromFrontendToJWT($authData);

        $jwt = $this->fetcher->getByAccessToken($accessToken);

        return $this->converter->fromJWTToFrontend($jwt);
    }

    public function refresh(string $authData): string
    {
        $refreshToken = $this->converter->fromFrontendToJWT($authData);

        $refreshWithAccessToken = $this->fetcher->getByRefreshToken($refreshToken);

        return $this->converter->fromJWTToFrontend($refreshWithAccessToken);
    }
}
