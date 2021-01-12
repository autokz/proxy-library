<?php


namespace Proxy\OAuth\Model\Access\Command\Logout;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Type("array")
     * @Assert\All(
     *     constraints={
     *           @Assert\Collection(
     *              fields={
     *                  "token_type" = @Assert\NotBlank(),
     *                  "expires_in" = @Assert\NotBlank(),
     *                  "access_token" = @Assert\NotBlank()
     *                  "refresh_token" = @Assert\NotBlank()
     *              }
     *          )
     *     }
     * )
     */
    public array $jwt;
}
