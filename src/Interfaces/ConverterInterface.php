<?php

declare(strict_types=1);

namespace Proxy\OAuth\Interfaces;

interface ConverterInterface
{
    public function fromFrontendToJWT(string $auth): array;

    public function fromJWTToFrontend(array $jwt): string;
}
