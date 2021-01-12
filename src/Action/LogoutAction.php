<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;


use Proxy\OAuth\Model\Access\Command\Check\Command;
use Proxy\OAuth\Model\Access\Command\Logout\Handler;
use Proxy\OAuth\Validator\Validator;

class LogoutAction
{
    private Handler $handler;
    private Validator $validator;

    public function __construct(Handler $handler, Validator $validator)
    {
        $this->validator = $validator;
        $this->handler = $handler;
    }

    public function handle(string $OAuthData): bool
    {
        $command = new Command();
        $command->OAuthData = $OAuthData;

        $this->validator->validate($command);

        $this->handler->handle($command);

        return true;
    }
}