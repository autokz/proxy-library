<?php

declare(strict_types=1);


namespace Test\Functional;

use PHPUnit\Framework\TestCase;
use Proxy\OAuth\Helpers\DotEnvConfigStorage;
use Proxy\OAuth\Helpers\GuzzleHttpClient;
use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Proxy;
use Proxy\OAuth\ReadModel\Access\JwtFetcher;
use Test\Builder\JwtConverterBuilder;

class WebTestCase extends TestCase
{
    protected Proxy $proxy;
    protected ConverterInterface $converter;
    protected JwtFetcher $fetcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = $converter = new JwtConverterBuilder();

        $configStore = new DotEnvConfigStorage(__DIR__ . '/../../');
        $configStore->load();

        $this->proxy = new Proxy($converter, $configStore);

        $this->fetcher = new JwtFetcher($configStore, new GuzzleHttpClient());
    }

}