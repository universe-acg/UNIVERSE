<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AcgEmailDomainValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        // Extract the domain from the email address
        [$username, $domain] = explode('@', $value);

        // Check if the domain matches the allowed domain
        if ($domain !== 'acg.edu') { // Change 'acg' to your desired domain
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
