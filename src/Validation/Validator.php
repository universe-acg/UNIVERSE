<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Validation;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Validator
{

    public static function validate($object, ExecutionContextInterface $context, $payload): void
    {
        global $kernel;
    }

    public static function validateEvent($object, ExecutionContextInterface $context, $payload): void
    {
        global $kernel;
    }
}
