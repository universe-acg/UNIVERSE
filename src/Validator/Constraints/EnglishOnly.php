<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EnglishOnly extends Constraint
{
    public $message = 'The text "{{ string }}" contains non-English characters.';
}