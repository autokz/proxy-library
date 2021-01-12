<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;

use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Model\Access\Command\Login\Command;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;
use Proxy\OAuth\ReadModel\Access\GetJwtFetcher;
use Proxy\OAuth\ReadModel\Access\JwtFetcher;
use Proxy\OAuth\Validator\Validator;

class LoginAction
{
    private JwtFetcher $fetcher;
    private Validator $validator;
    private ConverterInterface $converter;

    public function __construct(JwtFetcher $fetcher, ConverterInterface $converter)
    {
        $this->fetcher = $fetcher;
        $this->converter = $converter;
        $this->validator = new Validator();
    }

    public function handle(UsernameType $username, PasswordType $password): string
    {
        $command = new Command();
        $command->username = $username->getValue();
        $command->password = $password->getValue();

        $this->validator->validate($command);

        $jwt = $this->fetcher->getJwtByUsernamePassword($command);

        return $this->converter->fromJWTToFrontend($jwt);
    }
}
