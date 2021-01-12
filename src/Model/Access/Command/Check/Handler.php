<?php

namespace Proxy\OAuth\Model\Access\Command\Check;

use Proxy\OAuth\Helpers\Access\RefreshHelper;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStoreInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;

class Handler
{
    private ConverterInterface $converter;
    private ConfigStoreInterface $configStore;
    private HttpClientInterface $httpClient;
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

    public function handle(Command $command): void
    {
        $OAuthData = $command->OAuthData;

        $JWT = $this->converter->fromFrontendToJWT($OAuthData);

        if (!$this->check($JWT)) {
            $JWT = (new RefreshHelper($this->configStore, $this->httpClient))
                ->refresh($JWT);
        }

        $this->converter->fromJWTToFrontend($JWT);
    }

    private function check(?array $decryptedAuthData): bool
    {
        if (!$decryptedAuthData || isset($decryptedAuthData['access_token'])) {
            return false;
        }

        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $decryptedAuthData['access_token'],
        ];

        $responseClient = $this->httpClient->get($this->url, [], $headers, ['http_errors' => false]);

        return $responseClient->getStatusCode() === 200;
    }
}