<?php


namespace Proxy\OAuth\Helpers\Access;


use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;

class RefreshHelper
{
    private ConfigStorageInterface $config;
    private HttpClientInterface $httpClient;

    private string $url;

    public function __construct(ConfigStorageInterface $config, HttpClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;

        $baseUrl = trim($this->config->get('OAUTH_BASE_URL'), '/');
        $loginUrl = trim($this->config->get('OAUTH_URL'), '/');

        $this->url = $baseUrl . '/' . $loginUrl;
    }

    public function refresh(?array $decryptedAuthData): array
    {
        $refreshToken = $decryptedAuthData ?? [];
        $refreshToken = isset($refreshToken['refresh_token']) ? $refreshToken['refresh_token'] : '';

        $body = [
            'grant_type' => $this->config->get('OAUTH_REFRESH_GRANT_TYPE'),
            'refresh_token' => $refreshToken,
            'client_id' => $this->config->get('OAUTH_CLIENT_ID'),
            'client_secret' => $this->config->get('OAUTH_CLIENT_SECRET'),
        ];

        return json_decode($this->httpClient->post($this->url, $body, [])->getBody()->getContents(), true);
    }
}
