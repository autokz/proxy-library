<?php


namespace Proxy\OAuth\Model\Access\Command\Logout;

use InvalidArgumentException;
use Proxy\OAuth\Helpers\Access\RefreshHelper;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStoreInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Command\Check\Command;

class Handler
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

    public function handle(Command $command): void
    {
        $OAuthData = $command->OAuthData;

        $JWT = $this->converter->fromFrontendToJWT($OAuthData);

        if (!$JWT || !isset($JWT['refresh_token']) || !isset($JWT['access_token'])) {
            throw new InvalidArgumentException('Invalid OAuth data.');
        }

        if (!$this->logoutByAuthData($JWT)) {
            $this->logoutByRefreshToken($JWT);
        }
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
        $jwtFromRefresh = (new RefreshHelper($this->configStore, $this->httpClient))->refresh($decryptedAuthData);

        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $jwtFromRefresh['access_token']
        ];

        $this->httpClient->post($this->url, [], $headers);
    }
}
