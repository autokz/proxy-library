<?php

declare(strict_types=1);

namespace Proxy\OAuth\Action;

use Proxy\OAuth\Model\Access\Command\Login\Command;
use Proxy\OAuth\Model\Access\Command\Login\Handler;
use Proxy\OAuth\Model\Access\Type\PasswordType;
use Proxy\OAuth\Model\Access\Type\UsernameType;
use Proxy\OAuth\Validator\Validator;

class LoginAction
{
    private Handler $handler;
    private Validator $validator;

    public function __construct(Handler $handler, Validator $validator)
    {
        $this->validator = $validator;
        $this->handler = $handler;
    }

    public function handle(UsernameType $username, PasswordType $password): void
    {
        $command = new Command();
        $command->username = $username->getValue();
        $command->password = $password->getValue();

        $this->validator->validate($command);

        $this->handler->handle($command);
    }
}
