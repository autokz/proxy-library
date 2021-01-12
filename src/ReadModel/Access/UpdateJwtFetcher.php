<?php


namespace Proxy\OAuth\ReadModel\Access;

use Proxy\OAuth\Helpers\Access\RefreshHelper;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStoreInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Command\Check\Command;

class UpdateJwtFetcher
{
    private ConfigStoreInterface $configStore;
    private HttpClientInterface $httpClient;
    private string $url;

    public function __construct(
        ConfigStoreInterface $configStore,
        HttpClientInterface $httpClient = null
    ) {
        $this->configStore = $configStore;
        $this->httpClient = $httpClient ?? new GuzzleHttpClient();

        $baseUrl = trim($this->configStore->get('OAUTH_BASE_URL'), '/');
        $checkUrl = trim($this->configStore->get('OAUTH_CHECK_URL'), '/');

        $this->url = $baseUrl . '/' . $checkUrl;
    }

    public function updateJwt(Command $command): array
    {
        $Jwt = $command->jwt;

        if (!$this->check($Jwt)) {
            $Jwt = (new RefreshHelper($this->configStore, $this->httpClient))
                ->refresh($Jwt);
        }

        return $Jwt;
    }

    private function check(array $decryptedAuthData): bool
    {
        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $decryptedAuthData['access_token'],
        ];

        $responseClient = $this->httpClient->get($this->url, [], $headers, ['http_errors' => false]);

        return $responseClient->getStatusCode() === 200;
    }
}