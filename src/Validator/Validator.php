<?php


namespace Proxy\OAuth\Validator;

use Symfony\Component\Validator\Validation;

class Validator
{
    public function validate(object $object): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();

        $violations = $validator->validate($object);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }
}
