<?php


namespace Proxy\OAuth;


use Proxy\OAuth\Action\AccessAction;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStoreInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;

class Proxy
{
    private ConverterInterface $converter;
    private ConfigStoreInterface $configStore;
    private HttpClientInterface $httpClient;

    public function __construct(
        ConverterInterface $converter,
        ConfigStoreInterface $configStore,
        HttpClientInterface $httpClient = null
    ) {
        $this->converter = $converter;

        $configStore->load();
        $this->configStore = $configStore;

        $this->httpClient = $httpClient ?? new GuzzleHttpClient();
    }

    public function login(): void
    {
    }

    public function logout(): void
    {
    }

    public function check(): void
    {
        $OAuthData = $this->converter->fromFrontendToJWT();

        $accessAction = new AccessAction($this->configStore, $this->httpClient);
        $JWT = $accessAction->execute($OAuthData);

        $this->converter->fromJWTToFrontend($JWT);
    }
}