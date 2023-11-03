<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Profanity extends Constraint
{
    public $message = 'The text "{{ string }}" contains profanity.';
}