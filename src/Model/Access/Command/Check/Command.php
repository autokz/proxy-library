<?php


namespace Proxy\OAuth\Model\Access\Command\Check;

use Proxy\OAuth\Interfaces\ConverterInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Command
{
    private ConverterInterface $converter;

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

//    public string $jwt;
//
//    public function __construct(ConverterInterface $converter)
//    {
//        $this->converter = $converter;
//    }
//
//    public function isJwt(): bool
//    {
//        $arrJwt = $this->converter->fromFrontendToJWT($this->jwt);
//
//        if (array_key_exists('token_type', $arrJwt)) {
//            return true;
//        }
//
//        return false;
//    }
//
//    public static function loadValidatorMetadata(ClassMetadata $metadata): void
//    {
//        $metadata->addGetterConstraint(
//            'jwt',
//            new Assert\IsTrue(
//                [
//                    'message' => 'Invalid jwt.'
//                ]
//            )
//        );
//    }
}
