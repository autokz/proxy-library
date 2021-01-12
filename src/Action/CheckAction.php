<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;

use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Model\Access\Command\Check\Command;
use Proxy\OAuth\ReadModel\Access\JwtFetcher;
use Proxy\OAuth\Validator\Validator;

class CheckAction
{
    private JwtFetcher $fetcher;
    private ConverterInterface $converter;
    private Validator $validator;

    public function __construct(ConverterInterface $converter, JwtFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
        $this->converter = $converter;

        $this->validator = new Validator();
    }

    public function handle(string $OAuthData): string
    {
        $jwt = $this->converter->fromFrontendToJWT($OAuthData);

        $command = new Command();
        $command->jwt = $jwt;

        $this->validator->validate($command);

        $jwt = $this->fetcher->getByOAuthData($command);

        return $this->converter->fromJWTToFrontend($jwt);
    }
}
