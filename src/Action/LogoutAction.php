<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;

use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStoreInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;

class LogoutAction
{

    private ConverterInterface $converter;
    private HttpClientInterface $httpClient;
    private ConfigStoreInterface $configStore;
    private string $url;

    public function __construct(
        ConverterInterface $converter,
        ConfigStoreInterface $configStore,
        HttpClientInterface $httpClient = null
    ) {
        $this->converter = $converter;
        $this->configStore = $configStore;
        $this->httpClient = $httpClient ?? new GuzzleHttpClient();

        $baseUrl = trim($this->configStore->get('OAUTH_BASE_URL'), '/');
        $checkUrl = trim($this->configStore->get('OAUTH_LOGOUT_URL'), '/');
        $this->url = $baseUrl . '/' . $checkUrl;
    }

    public function execute(string $authData = ''): bool
    {
        $decryptedAuthData = $this->converter->fromFrontendToJWT($authData);

        if (!$decryptedAuthData || isset($decryptedAuthData['refresh_token']) || isset($decryptedAuthData['access_token'])) {
            return false;
        }

        if (!$this->logoutByAuthData($decryptedAuthData)) {
            $this->logoutByRefreshToken($decryptedAuthData);
        }

        return true;
    }

    private function logoutByAuthData(array $decryptedAuthData): bool
    {
        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $decryptedAuthData['access_token']
        ];

        $response = $this->httpClient->post($this->url, [], $headers, ['http_errors' => false]);

        return $response->getStatusCode() === 200;
    }

    private function logoutByRefreshToken(array $decryptedAuthData): void
    {
        $jwtFromRefresh = (new RefreshAction($this->configStore, $this->httpClient))->refresh($decryptedAuthData);

        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $jwtFromRefresh['access_token']
        ];

        $this->httpClient->post($this->url, [], $headers);
    }
}
