<?php

declare(strict_types=1);


namespace Test\Functional;

use PHPUnit\Framework\TestCase;
use Proxy\OAuth\Action\LoginAction;
use Proxy\OAuth\Helpers\DotEnvConfigStorage;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;
use Proxy\OAuth\ReadModel\Access\GetJwtFetcher;
use Proxy\OAuth\ReadModel\Access\JwtFetcher;
use Proxy\OAuth\Validator\Validator;
use Test\Builder\JwtConverterBuilder;

class WebTestCase extends TestCase
{

    protected ConverterInterface $converter;
    protected Validator $validator;
    protected GuzzleHttpClient $httpClient;
    protected DotEnvConfigStorage $configStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new JwtConverterBuilder();

        $this->validator = new Validator();

        $this->httpClient = new GuzzleHttpClient();

        $this->configStore = new DotEnvConfigStorage(__DIR__ . '/../../');
        $this->configStore->load();

        $this->fetcher = new JwtFetcher($this->configStore, $this->httpClient);
    }
}