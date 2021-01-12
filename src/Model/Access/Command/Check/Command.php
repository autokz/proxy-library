<?php


namespace Proxy\OAuth\Model\Access\Command\Check;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    public string $OAuthData;
}
