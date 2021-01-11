<?php

declare(strict_types=1);


namespace Test\Builder;


use Proxy\OAuth\Interfaces\ConverterInterface;

class JwtConverterBuilder implements ConverterInterface
{

    public function fromFrontendToJWT(string $auth): array
    {
        parse_str($auth, $authArr);

        foreach ($authArr as $key => $value) {
            $authArr[$key] = str_replace("Test", "", $value);
        }

        return $authArr;
    }

    public function fromJWTToFrontend(array $jwt): string
    {
        foreach ($jwt as $key => $value) {
            $jwt[$key] = $value . 'Test';
        }

        return http_build_query($jwt, '', '&');
    }
}