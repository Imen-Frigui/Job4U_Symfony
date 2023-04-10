<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Validate $constraint */

        if ($value > new \DateTime()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->format('d/m/Y'))
                ->addViolation();
        }
}
}
