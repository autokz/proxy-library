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
        new JwtType($jwt);
    }
}