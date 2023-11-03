<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProfanityValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $profanityList = file_get_contents(__DIR__ . '/../../../assets/profanity.txt'); // Adjust the path to your file
        $profanityWords = explode("\n", $profanityList);

        foreach ($profanityWords as $profanityWord) {
            $profanityWord = trim($profanityWord);

            if (stripos($value, $profanityWord) !== false) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
                break; // Break on the first profanity word found, or remove this line to find all profanity words in the text.
            }
        }
    }
}