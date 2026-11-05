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
    public function homepage(EventRepository $eventRepository, AppServices $services): Response
    {
        return $this->redirectToRoute('fos_user_security_login');
        $herosettings = $this->getDoctrine()->getManager()->getRepository(HomepageHeroSettings::class)->find(1);
        $entityManager = $this->getDoctrine()->getManager();

        $events = $eventRepository->findUpcomingEvents();

        $eventsToRemove = [];
        $eventsByCommonValue = [];

        foreach ($events as $event) {
            if ($event->isIsrepeat()) {
                $commonValue = $event->getCommonvalue();

                if (!isset($eventsByCommonValue[$commonValue])) {
                    $eventsByCommonValue[$commonValue] = [];
                }
                $eventsByCommonValue[$commonValue][] = $event;
            }
        }

        foreach ($eventsByCommonValue as $commonValue => $commonEvents) {
            $filteredEvents = array_filter($commonEvents, static function ($event) {
                return $event->getLatestEndDate() >= new DateTime();
            });

            usort($filteredEvents, static function ($eventA, $eventB) {
                $latestEventDateA = $eventA->getLatestEndDate();
                $latestEventDateB = $eventB->getLatestEndDate();

                if ($latestEventDateA === null && $latestEventDateB === null) {
                    return 0;
                }

                if ($latestEventDateA === null) {
                    return 1;
                }

                if ($latestEventDateB === null) {
                    return -1;
                }

                return $latestEventDateA <=> $latestEventDateB;
            });

            if (count($filteredEvents) > 1) {
                $eventsToRemove = array_merge($eventsToRemove, array_slice($filteredEvents, 1));
            }
        }

        $events = array_filter($events, static function ($event) use ($eventsToRemove) {
            return !in_array($event, $eventsToRemove, true);
        });
        $events = $this->sortByMostAttendees($events, $services);

        return $this->render('Front/Main/homepage.html.twig', [
            'herosettings' => $herosettings,
            'events' => $events,
        ]);
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
