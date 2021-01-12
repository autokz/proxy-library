<?php


namespace Proxy\OAuth\Model\Access\Command\Logout;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("array")
     */
    public array $jwt;
}
