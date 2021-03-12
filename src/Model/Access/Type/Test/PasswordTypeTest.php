<?php

namespace Proxy\OAuth\Model\Access\Type\Test;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Proxy\OAuth\Model\Access\Type\PasswordType;

class PasswordTypeTest extends TestCase
{
    public function testPasswordCorrect(): void
    {
        $passwordStr = 'password';

        $passwordType = new PasswordType($passwordStr);

        self::assertEquals($passwordStr, $passwordType->getValue());
    }

    public function testPasswordEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No password set.');

        new PasswordType('');
    }

    public function testPasswordShort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be more than 3 characters');

        new PasswordType('1');
    }
}
