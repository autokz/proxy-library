<?php

namespace Proxy\OAuth\Helpers\Access;

use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Type\JwtType;

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

    public function refresh(JwtType $jwt): array
    {
        $body = [
            'grant_type' => $this->config->get('OAUTH_REFRESH_GRANT_TYPE'),
            'refresh_token' => $jwt->getValue()['refresh_token'],
            'client_id' => $this->config->get('OAUTH_CLIENT_ID'),
            'client_secret' => $this->config->get('OAUTH_CLIENT_SECRET'),
        ];

        return json_decode($this->httpClient->post($this->url, $body, [])->getBody()->getContents(), true);
    }
}
