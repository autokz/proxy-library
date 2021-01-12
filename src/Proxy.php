<?php

declare(strict_types=1);


namespace Proxy\OAuth;


use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Command\Check\Command;
use Proxy\OAuth\ReadModel\Access\JwtFetcher;
use Proxy\OAuth\Validator\Validator;

class Proxy
{
    private ConverterInterface $converter;
    private ConfigStorageInterface $configStore;
    private HttpClientInterface $httpClient;
    private Validator $validator;
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

        $this->validator = new Validator();

        $this->fetcher = new JwtFetcher($this->configStore, $this->httpClient);
    }

    public function login(): void
    {
    }

    public function logout(): void
    {
    }

    public function check(string $authData): string
    {
        $jwtData = $this->converter->fromFrontendToJWT($authData);

        $command = new Command();
        $command->jwt = $jwtData;

        $this->validator->validate($command);

        $jwt = $this->fetcher->getByOAuthData($command);

        return $this->converter->fromJWTToFrontend($jwt);
    }
}