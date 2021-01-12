<?php


namespace Proxy\OAuth\Model\Access\Command\Login;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(min=3)
     */
    public string $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(min=3)
     */
    public string $password;
}
