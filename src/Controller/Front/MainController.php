<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Entity\Event;
use App\Entity\HomepageHeroSettings;
use App\Repository\EventRepository;
use App\Service\AppServices;
use DateTime;
use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{

    /**
     *
     */
#[Route("/", name: "homepage")]
    public function homepage(): Response
    {
        return $this->render('Front/Main/homepage.html.twig');
    }

    /**
     * Sort events by the count of attendees in descending order.
     *
     * @param Event[] $events
     * @return Event[]
     */

    private function sortByMostAttendees(array $events, $services): array
    {
        usort($events, function ($a, $b) use ($services) {
            return $services->getAttendeesBookedOfEvent($b->getSlug()) <=> $services->getAttendeesBookedOfEvent($a->getSlug());
        });

        return $events;
    }

    public function getEarliestEventDateStartDate($event)
    {
        $earliestStartDate = null;

        foreach ($event->getEventdates() as $eventDate) {

            $startDate = $eventDate->getStartdate();

            if ($earliestStartDate === null || $startDate < $earliestStartDate) {
                $earliestStartDate = $startDate;
            }
        }

        return $earliestStartDate;
    }

    public function getLatestEventDateEndDate($event)
    {
        $latestEndDate = null;

        foreach ($event->getEventdates() as $eventDate) {

            $endDate = $eventDate->getEnddate();

            if ($latestEndDate === null || $endDate > $latestEndDate) {
                $latestEndDate = $endDate;
            }
        }

        return $latestEndDate;
    }

}
