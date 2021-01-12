<?php

declare(strict_types=1);

namespace Proxy\OAuth\Model\Access\Type;

use Webmozart\Assert\Assert;

class UsernameType
{
    private string $username;

    public function __construct(string $username)
    {
        Assert::notEmpty($username, 'No Username set.');
        Assert::minLength($username, 3, 'Username must be more than 3 characters');
        $this->username = $username;
    }

    public function getValue(): string
    {
        return $this->username;
    }
}
