<?php

declare(strict_types=1);

namespace Proxy\OAuth\ReadModel\Access;

use PHPUnit\Exception;
use Proxy\OAuth\Helpers\Access\RefreshHelper;
use Proxy\OAuth\Interfaces\ConfigStorageInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
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

        $Jwt = (string)$this->httpClient->post($this->loginUrl, $body)->getBody();

        return json_decode($Jwt, true);
    }

    /**
     * @param array $jwt
     * @return array
     * @throws \Exception Throws when access token expired.
     */
    public function getByAccessToken(array $jwt): array
    {
        $this->check($jwt);
        return $jwt;
    }

    /**
     * @param array $jwt
     * @return array
     * @throws \Exception Throws when refresh token expired.
     */
    public function getByRefreshToken(array $jwt): array
    {
        return (new RefreshHelper($this->configStore, $this->httpClient))
            ->refresh($jwt);
    }

    private function check(array $jwt): bool
    {
        $headers = [
            'Authorization' => $this->configStore->get('OAUTH_TYPE') . ' ' . $jwt['access_token'],
        ];

        $responseClient = $this->httpClient->get($this->checkUrl, [], $headers);

        return $responseClient->getStatusCode() === 200;
    }
}
