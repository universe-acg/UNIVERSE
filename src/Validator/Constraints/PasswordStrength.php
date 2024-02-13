<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Validator\Constraints;
    
    use Symfony\Component\Validator\Constraint;
    
    /**
     * @Annotation
     */
    class PasswordStrength extends Constraint
    {
        public $message = 'Your password must meet the password strength requirements.';
    }