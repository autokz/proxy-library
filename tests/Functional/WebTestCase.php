<?php

declare(strict_types=1);


namespace Test\Functional;

use PHPUnit\Framework\TestCase;
use Proxy\OAuth\Action\LoginAction;
use Proxy\OAuth\Helpers\DotEnvConfigStorage;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Model\Access\Command\Login\Command;
use Proxy\OAuth\Model\Access\Command\Login\Handler;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;
use Proxy\OAuth\Validator\Validator;
use Test\Builder\JwtConverterBuilder;

class WebTestCase extends TestCase
{

    protected ConverterInterface $converter;
    protected string $username;
    protected string $password;
    protected GuzzleHttpClient $httpClient;
    protected DotEnvConfigStorage $configStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new JwtConverterBuilder();
        $this->username = 'tyanrv';
        $this->password = 'hash';

        $this->httpClient = new GuzzleHttpClient();

        $this->configStore = new DotEnvConfigStorage(__DIR__ . '/../../');
        $this->configStore->load();
    }

    protected function login(): void
    {
        $loginHandler = new Handler($this->converter, $this->configStore, $this->httpClient);
        $validator = new Validator();

        $username = new UsernameType('tyanrv');
        $password = new PasswordType('hash');

        $authAction = new LoginAction($loginHandler, $validator);

        $authAction->handle($username, $password);
    }
}