<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordStrengthValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // Implement your custom password strength validation logic here
        // You can check for minimum length, uppercase, lowercase, numbers, special characters, etc.

        // Example:
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/', $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}