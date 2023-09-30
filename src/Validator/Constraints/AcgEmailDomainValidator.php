<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Validator\Constraints;
    
    use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
    use Symfony\Component\Validator\Constraint;
    use Symfony\Component\Validator\ConstraintValidator;
    use UnexpectedValueException;
    
    class AcgEmailDomainValidator extends ConstraintValidator
    {
        public function validate($value, Constraint $constraint)
        {
            if (!$constraint instanceof AcgEmailDomain) {
                throw new UnexpectedTypeException($constraint, AcgEmailDomain::class);
            }
            
            if (null === $value || '' === $value) {
                return;
            }
            
            if (!is_string($value)) {
                throw new UnexpectedValueException($value, 'string');
            }
            
            // Validate the email format
            if (strpos($value, '@') === false) {
                $this->context->buildViolation('Invalid email format')
                    ->addViolation();
                return;
            }
            
            // Extract the domain from the email address
            [$username, $domain] = explode('@', $value);
            
            if ($domain !== 'acg.edu') { // Change 'acg' to your desired domain
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
