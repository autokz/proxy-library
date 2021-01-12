<?php


namespace Proxy\OAuth\Model\Access\Command\Login;


use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConfigStoreInterface;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Interfaces\HttpClientInterface;

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
        $loginUrl = trim($this->configStore->get('OAUTH_URL'), '/');

        $this->url = $baseUrl . '/' . $loginUrl;
    }

    public function handle(Command $command): void
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

        $responseClient = json_decode($this->httpClient->post($this->url, $body)->getBody()->getContents(), true);

        $this->converter->fromJWTToFrontend($responseClient);
    }
}