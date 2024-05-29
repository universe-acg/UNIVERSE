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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     */
    public function homepage(EventRepository $eventRepository, AppServices $services): Response
    {
        return $this->redirectToRoute('fos_user_security_login');
        $herosettings = $this->getDoctrine()->getManager()->getRepository(HomepageHeroSettings::class)->find(1);
        $entityManager = $this->getDoctrine()->getManager();

        // Query the Event entity to fetch the last 8 published events
        $events = $eventRepository->findUpcomingEvents();

        $eventsToRemove = [];
        $eventsByCommonValue = [];

        // Step 1: Iterate through each event
        foreach ($events as $event) {
            if ($event->isIsrepeat()) {
                $commonValue = $event->getCommonvalue();


                // Step 2: Create an array of events with the same common value
                if (!isset($eventsByCommonValue[$commonValue])) {
                    $eventsByCommonValue[$commonValue] = [];
                }
                $eventsByCommonValue[$commonValue][] = $event;
            }
        }

        // Step 3: Filter and sort events for each common value
        foreach ($eventsByCommonValue as $commonValue => $commonEvents) {
            $filteredEvents = array_filter($commonEvents, static function ($event) {
                return $event->getLatestEndDate() >= new DateTime();
            });

            // Sort the events based on the latest event date
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

            // Step 4: Add the first event to the removal list
            if (count($filteredEvents) > 1) {
                $eventsToRemove = array_merge($eventsToRemove, array_slice($filteredEvents, 1));
            }
        }

        // Step 5: Remove events with the same common value
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
            // Assuming there's a `getStartDate` method on your EventDate entity
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
            // Assuming there's a `getEndDate` method on your EventDate entity
            $endDate = $eventDate->getEnddate();

            if ($latestEndDate === null || $endDate > $latestEndDate) {
                $latestEndDate = $endDate;
            }
        }

        return $latestEndDate;
    }

}
