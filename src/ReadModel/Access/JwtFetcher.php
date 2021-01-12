<?php


namespace Proxy\OAuth\ReadModel\Access;

use Proxy\OAuth\Helpers\Access\RefreshHelper;
use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Command\Check\Command;
use Proxy\OAuth\Model\Access\Command\Login\Command as CommandLogin;

class JwtFetcher
{
    private ConfigStorageInterface $configStore;
    private HttpClientInterface $httpClient;
    private string $checkUrl;
    private string $loginUrl;

    public function __construct(
        ConfigStorageInterface $configStore,
        HttpClientInterface $httpClient
    ) {
        $this->configStore = $configStore;
        $this->httpClient = $httpClient;

        $baseUrl = trim($this->configStore->get('OAUTH_BASE_URL'), '/');

        $checkUrl = trim($this->configStore->get('OAUTH_CHECK_URL'), '/');
        $this->checkUrl = $baseUrl . '/' . $checkUrl;

        $loginUrl = trim($this->configStore->get('OAUTH_CHECK_URL'), '/');
        $this->loginUrl = $baseUrl . '/' . $loginUrl;
    }

    public function getJwt(CommandLogin $command): array
    {
        $username = $command->username;
        $password = $command->password;

        $body = [
            'grant_type' => $this->configStore->get('OAUTH_GRANT_TYPE'),
            'username' => $username,
            'password' => $password,
            'client_id' => $this->configStore->get('OAUTH_CLIENT_ID'),
            'client_secret' => $this->configStore->get('OAUTH_CLIENT_SECRET'),
            'access_type' => $this->configStore->get('OAUTH_ACCESS_TYPE'),
            'domain' => $this->configStore->get('OAUTH_DOMAIN')
        ];

        $Jwt = $this->httpClient->post($this->loginUrl, $body)->getBody()->getContents();

        return json_decode($Jwt, true);
    }

    public function getByOAuthData(Command $command): array
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

        $responseClient = $this->httpClient->get($this->checkUrl, [], $headers, ['http_errors' => false]);

        return $responseClient->getStatusCode() === 200;
    }
}