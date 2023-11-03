<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EnglishOnlyValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (preg_match('/[^A-Za-z0-9\s!@#$%^&*()-_+=<>?:"{}|,.\/;\'\[\]\\\`~]/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}