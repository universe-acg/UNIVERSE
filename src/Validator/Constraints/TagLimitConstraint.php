<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Validator\Constraints;
    
    use Symfony\Component\Validator\Constraint;
    
    class TagLimitConstraint extends Constraint
    {
        public $message = 'You can only enter up to {{ limit }} tags.';
        public $limit = 5;
        
        public function validatedBy()
        {
            return TagLimitValidator::class; // Make sure it points to the correct validator class
        }
    }