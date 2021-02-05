<?php

namespace Proxy\OAuth\Helpers\Access;

use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Type\JwtType;

class RefreshHelper
{
    private ConfigStorageInterface $config;
    private HttpClientInterface $httpClient;

    private string $refreshUrl;

    public function __construct(ConfigStorageInterface $config, HttpClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;

        $baseUrl = trim($this->config->get('OAUTH_BASE_URL'), '/');
        $loginUrl = trim($this->config->get('OAUTH_URL'), '/');

        $this->refreshUrl = $baseUrl . '/' . $loginUrl;
    }

    public function refresh($refreshToken): array
    {
        $body = [
            'grant_type' => $this->config->get('OAUTH_REFRESH_GRANT_TYPE'),
            'refresh_token' => $refreshToken,
            'client_id' => $this->config->get('OAUTH_CLIENT_ID'),
            'client_secret' => $this->config->get('OAUTH_CLIENT_SECRET'),
        ];

        return json_decode((string)$this->httpClient->post($this->refreshUrl, $body, [])->getBody(), true);
    }
}
