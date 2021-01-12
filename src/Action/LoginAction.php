<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;

use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Model\Access\Command\Login\Command;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;
use Proxy\OAuth\ReadModel\Access\GetJwtFetcher;
use Proxy\OAuth\Validator\Validator;

class LoginAction
{
    private GetJwtFetcher $fetcher;
    private Validator $validator;
    private ConverterInterface $converter;

    public function __construct(GetJwtFetcher $fetcher, Validator $validator, ConverterInterface $converter)
    {
        $this->validator = $validator;
        $this->fetcher = $fetcher;
        $this->converter = $converter;
    }

    public function handle(UsernameType $username, PasswordType $password): string
    {
        $command = new Command();
        $command->username = $username->getValue();
        $command->password = $password->getValue();

        $this->validator->validate($command);

        $Jwt = $this->fetcher->getJwt($command);

        return $this->converter->fromJWTToFrontend($Jwt);
    }
}
