<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Validator\Constraints;
    
    use Symfony\Component\Validator\Constraint;
    
    /**
     * @Annotation
     */
    class UniqueUsername extends Constraint
    {
        public $message = 'This username is already in use.';
    }
