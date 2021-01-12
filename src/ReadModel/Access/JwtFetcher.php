<?php


namespace Proxy\OAuth\ReadModel\Access;

use Proxy\OAuth\Helpers\Access\RefreshHelper;
use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Type\JwtType;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;

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

        $loginUrl = trim($this->configStore->get('OAUTH_URL'), '/');
        $this->loginUrl = $baseUrl . '/' . $loginUrl;
    }

    public function getJwtByUsernamePassword(UsernameType $username, PasswordType $password): array
    {
        $body = [
            'grant_type' => $this->configStore->get('OAUTH_GRANT_TYPE'),
            'username' => $username->getValue(),
            'password' => $password->getValue(),
            'client_id' => $this->configStore->get('OAUTH_CLIENT_ID'),
            'client_secret' => $this->configStore->get('OAUTH_CLIENT_SECRET'),
            'access_type' => $this->configStore->get('OAUTH_ACCESS_TYPE'),
            'domain' => $this->configStore->get('OAUTH_DOMAIN')
        ];

        $Jwt = $this->httpClient->post($this->loginUrl, $body)->getBody()->getContents();

        return json_decode($Jwt, true);
    }

    public function getByOAuthData(JwtType $jwt): array
    {
        $jwtArray = $jwt->getValue();

        if (!$this->check($jwtArray)) {
            $jwtArray = (new RefreshHelper($this->configStore, $this->httpClient))
                ->refresh($jwt);
        }

        return $jwtArray;
    }

    private function check(array $jwt): bool
    {
        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $jwt['access_token'],
        ];

        $responseClient = $this->httpClient->get($this->checkUrl, [], $headers, ['http_errors' => false]);

        return $responseClient->getStatusCode() === 200;
    }
}