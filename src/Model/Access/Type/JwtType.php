<?php

declare(strict_types=1);

namespace Proxy\OAuth\Model\Access\Type;

use Webmozart\Assert\Assert;

class JwtType
{
    private array $jwt;

    /**
     * JwtType constructor.
     * @param array $jwt
     */
    public function __construct(array $jwt)
    {
        Assert::keyExists($jwt, 'token_type', 'Jwt should contain key "token_type".');
        Assert::keyExists($jwt, 'expires_in', 'Jwt should contain key "expires_in".');
        Assert::keyExists($jwt, 'access_token', 'Jwt should contain key "access_token".');
        Assert::keyExists($jwt, 'refresh_token', 'Jwt should contain key "refresh_token".');

        Assert::notEmpty($jwt['token_type'], 'Jwt token_type should be not empty.');
        Assert::notEmpty($jwt['expires_in'], 'Jwt expires_in should be not empty.');
        Assert::notEmpty($jwt['access_token'], 'Jwt access_token should be not empty.');
        Assert::notEmpty($jwt['refresh_token'], 'Jwt refresh_token should be not empty.');
        $this->jwt = $jwt;
    }

    public function getValue(): array
    {
        return $this->jwt;
    }
}
