<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Validator\Constraints;
    
    use Symfony\Component\Validator\Constraint;
    
    /**
     * @Annotation
     */
    class UniqueEmail extends Constraint
    {
        public $message = 'This email is already in use.';
    }
