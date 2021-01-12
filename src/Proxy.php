<?php

declare(strict_types=1);


namespace Proxy\OAuth;


use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Type\JwtType;
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

    public function logout(): void
    {
    }

    public function check(string $authData): string
    {
        $jwtData = $this->converter->fromFrontendToJWT($authData);

        $jwt = $this->fetcher->getByOAuthData(new JwtType($jwtData));

        return $this->converter->fromJWTToFrontend($jwt);
    }
}