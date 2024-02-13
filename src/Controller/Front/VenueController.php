<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\AppServices;
use App\Service\VenueService;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VenueController extends AbstractController
{
    private $venueService;

    public function __construct(VenueService $venueService)
    {
        $this->venueService = $venueService;
    }

    /**
     * @Route("/venues", name="venues")
     */
    public function venues(Request $request, PaginatorInterface $paginator, AppServices $services): Response
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
        $venues = $paginator->paginate($services->getVenues(array('keyword' => $keyword)), $request->query->getInt('page', 1), 28);
        return $this->render('Front/Venue/venues.html.twig', [
            'venues' => $venues
        ]);
    }

    /**
     * @Route("/venue/{slug}", name="venue")
     */
    public function venueBySlug($slug, AppServices $services, EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Retrieve the venue by slug from the service or repository
        $venue = $this->venueService->getVenueBySlug($slug);
        $events = $this->getevents($services, $eventRepository, $paginator, $request, $venue);

        // Check if the venue exists
        if (!$venue) {
            throw $this->createNotFoundException('Venue not found');
        }

        return $this->render('Front/Venue/venue_details.html.twig', [
            'venue' => $venue,
            'events' => $events
        ]);
    }

    private function getevents($services, $eventRepository, $paginator, $request, $venue)
    {
        $events = $services->getEventsHomepageIndex([
            "elapsed" => "all",
            "venue" => $venue,
            'published' => true,
        ])->getQuery()->getResult();
        $attendedEventIds = [];
        $attendedEvents = $eventRepository->findAttendedEventsByUser($this->getUser(), "all", "all")->getQuery()->getResult();

        foreach ($attendedEvents as $attendedEvent) {
            $attendedEventIds[] = $attendedEvent->getId();
        }

        foreach ($events as $event) {
            if (in_array($event->getId(), $attendedEventIds, true)) {
                $event->setIsattended(true);
            } else {
                $event->setIsattended(false);
            }
        }

//        $events = $this->sortByMostAttendees($events, $services);

        $eventsall = $services->getEventsHomepageIndex([
            "elapsed" => "all",
            'published' => true,
        ])->getQuery()->getResult();


        $totaltags = [];
        // Loop through the events and split the 'tags' string into an array
        foreach ($eventsall as $event) {
            $tags = explode(',', $event->getTags()); // Assuming 'getTags()' method exists in your entity

            // Add the non-empty tags from this event to the $totaltags array
            foreach ($tags as $tag) {
                $tag = trim($tag); // Remove leading/trailing spaces
                if (!empty($tag)) {
                    $totaltags[] = $tag;
                }
            }
        }

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

        $currentDateTime = new DateTime();

// Separate future and past events
        $futureEvents = [];
        $pastEvents = [];

        foreach ($events as $event) {
            if ($this->getLatestEventDateEndDate($event) >= $currentDateTime) {
                $futureEvents[] = $event;
            } else {
                $pastEvents[] = $event;
            }
        }

// Sort each group separately based on your sorting criteria
        usort($futureEvents, function ($a, $b) {
            // Sorting logic for future events (e.g., most recent first)
            return $this->getLatestEventDateEndDate($a) <=> $this->getLatestEventDateEndDate($b);
        });

        usort($pastEvents, function ($a, $b) {
            // Sorting logic for past events (e.g., most recent first)
            return $this->getLatestEventDateEndDate($b) <=> $this->getLatestEventDateEndDate($a);
        });
        $sortedEvents = array_merge($futureEvents, $pastEvents);

// Combine the two sorted arrays, placing future events before past events

        $eventsMergedPaginated = $paginator->paginate(
            $sortedEvents ?? $events,
            $request->query->getInt('page', 1),
            $services->getSetting("events_per_page"),
            ['wrap-queries' => true]
        );

        return $eventsMergedPaginated;
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
}
