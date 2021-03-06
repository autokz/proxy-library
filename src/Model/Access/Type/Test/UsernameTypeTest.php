<?php

namespace Proxy\OAuth\Model\Access\Type\Test;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Proxy\OAuth\Model\Access\Type\UsernameType;

class UsernameTypeTest extends TestCase
{
    public function testUsernameCorrect(): void
    {
        $passwordStr = 'username';

        $passwordType = new UsernameType($passwordStr);

        self::assertEquals($passwordStr, $passwordType->getValue());
    }

    public function testUsernameEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No Username set.');

        new UsernameType('');
    }

    public function testUsernameShort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username must be more than 3 characters');

        new UsernameType('1');
    }
}
