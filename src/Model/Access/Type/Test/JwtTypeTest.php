<?php

declare(strict_types=1);

namespace Proxy\OAuth\Model\Access\Type\Test;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Proxy\OAuth\Model\Access\Type\JwtType;

class JwtTypeTest extends TestCase
{
    public static array $JWT = [
        'token_type' => 'Bearer',
        'expires_in' => '3600',
        'access_token' => 'tokenAccess',
        'refresh_token' => 'tokenRefresh'
    ];

    public function testNotExistTokenType(): void
    {
        $jwt = self::$JWT;
        unset($jwt['token_type']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Jwt should contain key "token_type".');
        new JwtType($jwt);
    }

    public function testNotExistExpiresIn(): void
    {
        $jwt = self::$JWT;
        unset($jwt['expires_in']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Jwt should contain key "expires_in".');
        new JwtType($jwt);
    }

    public function testNotExistAccessToken(): void
    {
        $jwt = self::$JWT;
        unset($jwt['access_token']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Jwt should contain key "access_token".');
        new JwtType($jwt);
    }

    public function testNotExistRefreshToken(): void
    {
        $jwt = self::$JWT;
        unset($jwt['refresh_token']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Jwt should contain key "refresh_token".');
        new JwtType($jwt);
    }
}
