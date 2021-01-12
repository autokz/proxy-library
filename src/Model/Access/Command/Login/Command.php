<?php


namespace Proxy\OAuth\Model\Access\Command\Login;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\string()
     */
    public string $jwt;
}
