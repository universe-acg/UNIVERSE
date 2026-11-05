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
use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VenueController extends AbstractController
{
    private $venueService;

    public function __construct(VenueService $venueService)
    {
        $this->venueService = $venueService;
    }

    /**
     *
     */
#[Route("/venues", name: "venues")]
    public function venues(Request $request, PaginatorInterface $paginator, AppServices $services, \App\Service\PublicCatalogue $catalogue): Response
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
        $venues = $paginator->paginate($catalogue->filter($services->getVenues([]), $request->query->getString('keyword'), $request->query->getString('sortedby', 'created_desc')), max(1, $request->query->getInt('page', 1)), 24);
        return $this->render('Front/Venue/venues.html.twig', [
            'venues' => $venues
        ]);
    }

    /**
     *
     */
#[Route("/venue/{slug}", name: "venue")]
    public function venueBySlug($slug, AppServices $services, EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $venue = $this->venueService->getVenueBySlug($slug);
        $events = $this->getevents($services, $eventRepository, $paginator, $request, $venue);

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


        $eventsall = $services->getEventsHomepageIndex([
            "elapsed" => "all",
            'published' => true,
        ])->getQuery()->getResult();


        $totaltags = [];

        foreach ($eventsall as $event) {
            $tags = explode(',', $event->getTags()); // Assuming 'getTags()' method exists in your entity

            foreach ($tags as $tag) {
                $tag = trim($tag); // Remove leading/trailing spaces
                if (!empty($tag)) {
                    $totaltags[] = $tag;
                }
            }
        }

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

        $currentDateTime = new DateTime();

        $futureEvents = [];
        $pastEvents = [];

        foreach ($events as $event) {
            if ($this->getLatestEventDateEndDate($event) >= $currentDateTime) {
                $futureEvents[] = $event;
            } else {
                $pastEvents[] = $event;
            }
        }

        usort($futureEvents, function ($a, $b) {

            return $this->getLatestEventDateEndDate($a) <=> $this->getLatestEventDateEndDate($b);
        });

        usort($pastEvents, function ($a, $b) {

            return $this->getLatestEventDateEndDate($b) <=> $this->getLatestEventDateEndDate($a);
        });
        $sortedEvents = array_merge($futureEvents, $pastEvents);


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
