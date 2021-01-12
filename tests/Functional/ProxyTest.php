<?php

declare(strict_types=1);


namespace Test\Functional;


class ProxyTest extends WebTestCase
{



    private function login(): string
    {

        $authAction = new LoginAction($this->fetcher, $this->validator, $this->converter);

        $username = new UsernameType('tyanrv');
        $password = new PasswordType('hash');

        return $authAction->handle($username, $password);
    }
}