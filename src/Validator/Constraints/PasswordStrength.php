<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordStrength extends Constraint
{
    public $message = 'Your password must meet the password strength requirements.';
}