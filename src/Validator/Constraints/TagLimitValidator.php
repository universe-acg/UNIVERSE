<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Validator\Constraints;
    
    use Symfony\Component\Validator\Constraint;
    use Symfony\Component\Validator\ConstraintValidator;
    
    class TagLimitValidator extends ConstraintValidator
    {
        public function validate($value, Constraint $constraint)
        {
            $tags = explode(',', $value);
            
            if (count($tags) > $constraint->limit) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ limit }}', $constraint->limit)
                    ->addViolation();
            }
        }
    }