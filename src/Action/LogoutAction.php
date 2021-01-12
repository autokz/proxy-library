<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;


use Proxy\OAuth\Interfaces\ConverterInterface;
use Proxy\OAuth\Model\Access\Command\Logout\Command;
use Proxy\OAuth\Model\Access\Command\Logout\Handler;
use Proxy\OAuth\Validator\Validator;

class LogoutAction
{
    private Handler $handler;
    private Validator $validator;
    private ConverterInterface $converter;

    public function __construct(Handler $handler, Validator $validator, ConverterInterface $converter)
    {
        $this->validator = $validator;
        $this->handler = $handler;
        $this->converter = $converter;
    }

    public function handle(string $OAuthData): bool
    {
        $Jwt = $this->converter->fromFrontendToJWT($OAuthData);

        $command = new Command();
        $command->jwt = $Jwt;

        $this->validator->validate($command);

        $this->handler->handle($command);

        return true;
    }
}