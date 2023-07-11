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
        $defaultlocalecheck = false;

        foreach ($object->getTranslations() as $translation) {
            if ($translation->getLocale() === $kernel->getContainer()->getParameter('locale')) {
                $defaultlocalecheck = true;
                break;
            }
        }

        if (!$defaultlocalecheck) {
            $context->buildViolation('You must set the default locale at least for the translation fields')
                ->atPath('Basic')
                ->addViolation();
        }
    }

    public static function validateEvent($object, ExecutionContextInterface $context, $payload): void
    {

        global $kernel;
        $defaultlocalecheck = false;

        foreach ($object->getTranslations() as $translation) {
            if ($translation->getLocale() === $kernel->getContainer()->getParameter('locale')) {
                $defaultlocalecheck = true;
                break;
            }
        }

        if (!$defaultlocalecheck) {
            $context->buildViolation('You must set the default locale at least for the translation fields')
                ->atPath('Basic')
                ->addViolation();
        }

        foreach ($object->getEventdates() as $indexEventDate => $eventDate) {
            if (!$eventDate->getOnline() && !$eventDate->getVenue()) {
                $context->buildViolation('This value should not be blank.')
                    ->atPath('eventdates[' . $indexEventDate . '].venue')
                    ->addViolation();
            }
            foreach ($eventDate->getTickets() as $indexDateTicket => $eventDateTicket) {
                if (!$eventDateTicket->getFree() && !$eventDateTicket->getPrice()) {
                    $context->buildViolation('This value should not be blank.')
                        ->atPath('eventdates[' . $indexEventDate . '].tickets[' . $indexDateTicket . '].price')
                        ->addViolation();
                }
            }
        }
    }
}
