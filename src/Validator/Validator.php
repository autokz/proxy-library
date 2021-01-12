<?php


namespace Proxy\OAuth\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(object $object): void
    {
        $violations = $this->validator->validate($object);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }
}
