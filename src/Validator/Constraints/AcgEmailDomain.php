<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

// src/Validator/Constraints/AcgEmailDomain.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AcgEmailDomain extends Constraint
{
    public $message = 'Only email addresses from the @acg domain are allowed.';
}
