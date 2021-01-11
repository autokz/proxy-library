<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;

use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStoreInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;

class AccessAction
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
        $checkUrl = trim($this->configStore->get('OAUTH_CHECK_URL'), '/');

        $this->url = $baseUrl . '/' . $checkUrl;
    }

    public function execute(string $authData = ''): string
    {
        $decryptedAuthData = $this->converter->fromFrontendToJWT($authData);
        if (!$this->check($decryptedAuthData)) {
            $jwtFromRefresh = (new RefreshAction($this->configStore, $this->httpClient))
                ->refresh($decryptedAuthData);
            return $this->converter->fromJWTToFrontend($jwtFromRefresh);
        }
        return $authData;
    }

    private function check(?array $decryptedAuthData): bool
    {
        if(!$decryptedAuthData || isset($decryptedAuthData['access_token'])) {
            return false;
        }

        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $decryptedAuthData['access_token'],
        ];

        $responseClient = $this->httpClient->get($this->url, [], $headers, ['http_errors' => false]);

        return $responseClient->getStatusCode() === 200;
    }
}
