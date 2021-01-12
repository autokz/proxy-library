<?php


namespace Proxy\OAuth\Model\Access\Command\Logout;

use Proxy\OAuth\Helpers\Access\RefreshHelper;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Type\JwtType;

class Handler
{

    private ConverterInterface $converter;
    private HttpClientInterface $httpClient;
    private ConfigStorageInterface $configStore;
    private string $url;

    public function __construct(
        ConverterInterface $converter,
        ConfigStorageInterface $configStore,
        HttpClientInterface $httpClient = null
    ) {
        $this->converter = $converter;
        $this->configStore = $configStore;
        $this->httpClient = $httpClient ?? new GuzzleHttpClient();

        $baseUrl = trim($this->configStore->get('OAUTH_BASE_URL'), '/');
        $checkUrl = trim($this->configStore->get('OAUTH_LOGOUT_URL'), '/');
        $this->url = $baseUrl . '/' . $checkUrl;
    }

    public function handle(JwtType $jwt): void
    {
        if (!$this->logoutByAuthData($jwt)) {
            $this->logoutByRefreshToken($jwt);
        }
    }

    private function logoutByAuthData(JwtType $jwt): bool
    {
        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $jwt->getValue()['access_token']
        ];

        $response = $this->httpClient->post($this->url, [], $headers, ['http_errors' => false]);

        return $response->getStatusCode() === 200;
    }

    private function logoutByRefreshToken(JwtType $jwt): void
    {
        $jwtFromRefresh = (new RefreshHelper($this->configStore, $this->httpClient))->refresh($jwt);

        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $jwtFromRefresh['refresh_token']
        ];

        $this->httpClient->post($this->url, [], $headers);
    }
}
