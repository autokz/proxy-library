<?php


namespace Proxy\OAuth\ReadModel\Access;


use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStoreInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;
use Proxy\OAuth\Model\Access\Command\Login\Command;

class GetJwtFetcher
{
    private HttpClientInterface $httpClient;
    private ConfigStoreInterface $configStore;

    private string $url;

    public function __construct(
        ConfigStoreInterface $configStore,
        HttpClientInterface $httpClient = null
    ) {
        $this->configStore = $configStore;
        $this->httpClient = $httpClient ?? new GuzzleHttpClient();

        $baseUrl = trim($this->configStore->get('OAUTH_BASE_URL'), '/');
        $loginUrl = trim($this->configStore->get('OAUTH_URL'), '/');

        $this->url = $baseUrl . '/' . $loginUrl;
    }

    public function getJwt(Command $command): array
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

        $Jwt = $this->httpClient->post($this->url, $body)->getBody()->getContents();

        return json_decode($Jwt, true);
    }
}