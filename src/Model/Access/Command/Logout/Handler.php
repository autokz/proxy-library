<?php

namespace Proxy\OAuth\Model\Access\Command\Logout;

use Exception;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;

class Handler
{
    private ConverterInterface $converter;
    private HttpClientInterface $httpClient;
    private ConfigStorageInterface $configStore;
    private string $url;

    public function __construct(
        ConverterInterface $converter,
        ConfigStorageInterface $configStore,
        HttpClientInterface $httpClient
    ) {
        $this->converter = $converter;
        $this->configStore = $configStore;
        $this->httpClient = $httpClient;

        $baseUrl = trim($this->configStore->get('OAUTH_BASE_URL'), '/');
        $logoutUrl = trim($this->configStore->get('OAUTH_LOGOUT_URL'), '/');
        $this->url = $baseUrl . '/' . $logoutUrl;
    }

    /**
     * @param array $jwt
     * @throws Exception Throws when access token expired.
     */
    public function handle(array $jwt): void
    {
        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $jwt['access_token']
        ];

        $this->httpClient->post($this->url, [], $headers);
    }

}
