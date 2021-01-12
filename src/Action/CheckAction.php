<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;

use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Model\Access\Command\Check\Command;
use Proxy\OAuth\ReadModel\Access\UpdateJwtFetcher;
use Proxy\OAuth\Validator\Validator;

class CheckAction
{
    private UpdateJwtFetcher $fetcher;
    private Validator $validator;
    private ConverterInterface $converter;

    public function __construct(UpdateJwtFetcher $fetcher, Validator $validator, ConverterInterface $converter)
    {
        $this->validator = $validator;
        $this->fetcher = $fetcher;
        $this->converter = $converter;
    }

    public function handle(string $OAuthData): string
    {
        $Jwt = $this->converter->fromFrontendToJWT($OAuthData);

        $command = new Command();
        $command->jwt = $Jwt;

        $this->validator->validate($command);

        $Jwt = $this->fetcher->updateJwt($command);

        return $this->converter->fromJWTToFrontend($Jwt);
    }
}
