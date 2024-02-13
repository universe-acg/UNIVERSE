<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Entity\Event;
use App\Entity\File;
use App\Repository\EventFileRepository;
use App\Repository\EventRepository;
use App\Repository\EventTicketRepository;
use App\Repository\OrderElementRepository;
use App\Repository\OrderRepository;
use App\Service\AppServices;
use App\Service\ListingService;
use App\Service\VenueService;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
//        /**
//         * @Route("/events", name="events")
//         * @throws InvalidDatabaseException|InvalidArgumentException
//         */
//        public function events(Request $request, PaginatorInterface $paginator, AppServices $services): Response
//        {
//            // Get the current date and time
//            $now = new DateTime();
//
//// Set the initial time to midnight (00:00:00) and max time to 23:59:59
//            $todayStart = clone $now->setTime(0, 0, 0);
//            $todayEnd = clone $now->setTime(23, 59, 59);
//
//// Move to tomorrow
//            $tomorrow = $now->modify('+1 day');
//// Set the initial time to midnight (00:00:00) and max time to 23:59:59
//            $tomorrowStart = clone $tomorrow->setTime(0, 0, 0);
//            $tomorrowEnd = clone $tomorrow->setTime(23, 59, 59);
//
//// Start of this week (assuming Monday is the start of the week)
//            $thisWeekStartDate = clone (new DateTime())->modify('previous Monday');
//// End of this week (assuming Sunday is the end of the week)
//            $thisWeekEndDate = clone (new DateTime())->modify('next Sunday')->setTime(23, 59, 59);
//
//// Start of next week
//            $nextWeekStartDate = clone (new DateTime())->modify('next Monday')->modify('+7 days');
//// End of next week
//            $nextWeekEndDate = clone (new DateTime())->modify('next Sunday')->modify('+7 days')->setTime(23, 59, 59);
//
//// Start of this month
//            $thisMonthStartDate = clone (new DateTime())->modify('first day of this month');
//// End of this month
//            $thisMonthEndDate = clone (new DateTime())->modify('last day of this month')->setTime(23, 59, 59);
//
//// Start of next month
//            $nextMonthStartDate = clone (new DateTime())->modify('first day of next month');
//// End of next month
//            $nextMonthEndDate = clone (new DateTime())->modify('last day of next month')->setTime(23, 59, 59);
//
//// Get event counts for the date ranges
//            $todayEventCount = $services->getEventCountForDateRange($todayStart, $todayEnd);
//            $tomorrowEventCount = $services->getEventCountForDateRange($tomorrowStart, $tomorrowEnd);
//            $thisWeekEventCount = $services->getEventCountForDateRange($thisWeekStartDate, $thisWeekEndDate);
//            $nextWeekEventCount = $services->getEventCountForDateRange($nextWeekStartDate, $nextWeekEndDate);
//            $thisMonthEventCount = $services->getEventCountForDateRange($thisMonthStartDate, $thisMonthEndDate);
//            $nextMonthEventCount = $services->getEventCountForDateRange($nextMonthStartDate, $nextMonthEndDate);
//
//            $startDateMin = clone $tomorrow;
//            $startDateMin->setTime(0, 0, 0); // Set to midnight
//            $startDateMax = clone $tomorrow;
//            $startDateMax->setTime(23, 59, 59); // Set to end of the day
//
//            $category = $request->query->get('category') ?: "all";
//            $keyword = $request->query->get('keyword') == "" ? "all" : $request->query->get('keyword');
//            $startdate = $request->query->get('startdate') == "" ? "all" : $request->query->get('startdate');
//            $organizer = $request->query->get('organizer') == "" ? "all" : $request->query->get('organizer');
//
//            if ($category != "all") {
//                $category = $services->getCategories(array("slug" => $category))->getQuery()->getOneOrNullresult();
//                if (!$category) {
//                    $this->addFlash('error', 'The category can not be found');
//                    return $this->redirectToRoute('events');
//                }
//            }
//
////            $eventsupcoming = $paginator->paginate($services->getEventsHomepageIndex(array("elapsed" => false, "category" => $category, 'keyword' => $keyword, 'startdate' => $startdate, 'organizer' => $organizer, 'onlineonly' => $onlineonly))->getQuery(), $request->query->getInt('page', 1), $services->getSetting("events_per_page"), array('wrap-queries' => true));
////            $eventsold = $paginator->paginate($services->getEventsHomepageIndex(array("elapsed" => "all", "category" => $category, 'keyword' => $keyword, 'startdate' => $startdate, 'organizer' => $organizer, 'onlineonly' => $onlineonly))->getQuery(), $request->query->getInt('page', 1), $services->getSetting("events_per_page"), array('wrap-queries' => true));
//
//            // Fetch events that will happen (upcoming)
//            $eventsUpcoming = $services->getEventsHomepageIndex([
//                "elapsed" => false,
//                "category" => $category,
//                'keyword' => $keyword,
//                'startdate' => $startdate,
//                'organizer' => $organizer,
//            ])->getQuery()->getResult();
//
//// Fetch events that have happened (old)
//            $eventsOld = $services->getEventsHomepageIndex([
//                "elapsed" => true,
//                "category" => $category,
//                'keyword' => $keyword,
//                'startdate' => $startdate,
//                'organizer' => $organizer,
//            ])->getQuery()->getResult();
//
//            // Fetch events
//// Iterate through events and set isPassed based on start date
//            foreach ($eventsOld as $event) {
//                $event->setIsPassed(true);
//            }
//
//// Merge the two sets of events
//            // Merge the two sets of events while avoiding duplicates
//            $mergedEvents = [];
//
//            foreach ($eventsUpcoming as $event) {
//                $mergedEvents[$event->getId()] = $event;
//            }
//
//            foreach ($eventsOld as $event) {
//                $mergedEvents[$event->getId()] = $event;
//            }
//
//// Sort the merged events by startdate (ascending order)
//            usort($mergedEvents, static function ($a, $b) {
//                return $b->getEventdates()[0]->getStartdate() <=> $a->getEventdates()[0]->getStartdate();
//            });
//
//// Paginate the sorted merged events
//            $eventsMergedPaginated = $paginator->paginate(
//                $mergedEvents,
//                $request->query->getInt('page', 1),
//                $services->getSetting("events_per_page"),
//                ['wrap-queries' => true]
//            );
//
//            return $this->render('Front/Event/events.html.twig', [
//                'events' => $eventsMergedPaginated,
//                'category' => $category,
//                'todayEventCount' => $todayEventCount,
//                'tomorrowEventCount' => $tomorrowEventCount,
//                'thisWeekEventCount' => $thisWeekEventCount,
//                'nextWeekEventCount' => $nextWeekEventCount,
//                'thisMonthEventCount' => $thisMonthEventCount,
//                'nextMonthEventCount' => $nextMonthEventCount,
//            ]);
//        }
    /**
     * @Route("/events", name="events")
     * @throws InvalidDatabaseException|InvalidArgumentException
     */
    public function events(Request $request, VenueService $venueService, EventRepository $eventRepository, PaginatorInterface $paginator, AppServices $services): Response
    {
        $now = new DateTime();
        $sortedby = $request->get('sortedby');

        $todayRange = $this->getDateRange('today');
        $tomorrowRange = $this->getDateRange('tomorrow');
        $thisWeekRange = $this->getDateRange('previous Monday', 'next Sunday');
        $nextWeekRange = $this->getDateRange('Monday next week', ' next Sunday');
        $thisMonthRange = $this->getDateRange('first day of this month', 'last day of this month');
        $nextMonthRange = $this->getDateRange('first day of next month', 'last day of this month');

        $todayEventCount = $services->getEventCountForDateRange($todayRange['start'], $todayRange['end']);
        $tomorrowEventCount = $services->getEventCountForDateRange($tomorrowRange['start'], $tomorrowRange['end']);
        $thisWeekEventCount = $services->getEventCountForDateRange($thisWeekRange['start'], $thisWeekRange['end']);
        $nextWeekEventCount = $services->getEventCountForDateRange($nextWeekRange['start'], $nextWeekRange['end']);
        $thisMonthEventCount = $services->getEventCountForDateRange($thisMonthRange['start'], $thisMonthRange['end']);
        $nextMonthEventCount = $services->getEventCountForDateRange($nextMonthRange['start'], $nextMonthRange['end']);

        // Get the current date
        $currentDate = new DateTime();

        $tomorrow = clone $currentDate;
        $tomorrow->modify('+1 day');

        $tomorrow->setTime(0, 0, 0);

        $startDateMin = clone $tomorrow;
        $startDateMin->setTime(0, 0, 0); // Set to midnight
        $startDateMax = clone $tomorrow;
        $startDateMax->setTime(23, 59, 59); // Set to end of the day

        $category = $request->query->get('category') ?: "all";
        $venue = $request->query->get('venue') ?: "all";
        $keyword = $request->query->get('keyword') == "" ? "all" : $request->query->get('keyword');
        $organizer = $request->query->get('organizer') == "" ? "all" : $request->query->get('organizer');
        $startdate = $request->query->get('startdate') == "" ? "all" : $request->query->get('startdate');

        $now = new DateTime();

        switch ($startdate) {
            case "today":
                $start = clone $now->setTime(0, 0, 0);
                $end = clone $now->setTime(23, 59, 59);
                break;
            case "tomorrow":
                $tomorrow = $now->modify('+1 day');
                $start = clone $tomorrow->setTime(0, 0, 0);
                $end = clone $tomorrow->setTime(23, 59, 59);
                break;
            case "thisweek":
                $start = clone (new DateTime())->modify('previous Monday');
                $end = clone (new DateTime())->modify('next Sunday')->setTime(23, 59, 59);
                break;
            case "nextweek":
                $start = clone (new DateTime())->modify('Monday next week');
                $end = clone (new DateTime())->modify('Sunday next week')->setTime(23, 59, 59);
                break;
            case "thismonth":
                $start = clone (new DateTime())->modify('first day of this month');
                $end = clone (new DateTime())->modify('last day of this month')->setTime(23, 59, 59);
                break;
            case "nextmonth":
                $start = clone (new DateTime())->modify('first day of next month');
                $end = clone (new DateTime())->modify('last day of next month')->setTime(23, 59, 59);
                break;
            default:
                $start = null;
                $end = null;
                break;
        }

        if ($category != "all") {
            $category = $services->getCategories(array("slug" => $category))->getQuery()->getOneOrNullresult();
            if (!$category) {
                $this->addFlash('error', 'The category can not be found');
                return $this->redirectToRoute('events');
            }
        }

        if ($venue != "all") {
            $venue = $venueService->getVenues(array("slug" => $venue));
            if (!$venue) {
                $this->addFlash('error', 'The venue can not be found');
                return $this->redirectToRoute('events');
            }
        }

        $events = $services->getEventsHomepageIndex([
            "elapsed" => "all",
            "category" => $category,
            "venue" => $venue,
            'keyword' => $keyword,
            'published' => true,
            'startdate' => $start,
            'enddate' => $end,
            'organizer' => $organizer,
            'sortedby' => $sortedby
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

        if ($sortedby == 'mostbooked') {
            $events = $this->sortByMostAttendees($events, $services);
        } elseif ($sortedby == 'leastbooked') {
            $events = $this->sortByLeastAttendees($events, $services);
        }

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

        $open_events_only = $request->query->get('open-events-only'); // open
        $hide_repeating = $request->query->get('hide-repeating'); // isrepeat
        $help_events_only = $request->query->get('help-events-only'); // volsneeded

        if ($open_events_only) {
            $events = array_filter($events, static function ($event) {
                return $event->getOpen() === true;
            });
        }

        $outofcampusonly = $request->query->get('out-of-campus'); // open
        if ($outofcampusonly) {
            $events = array_filter($events, static function ($event) {
                return $event->getOutofcampus() === true;
            });
        }

        if (!$hide_repeating) {
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
        }

        if ($help_events_only) {
            $events = array_filter($events, static function ($event) {
                return $event->isVolsneeded() == true;
            });
        }

        if (!$sortedby || $sortedby == "all") {
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
        }

// Combine the two sorted arrays, placing future events before past events

        $eventsMergedPaginated = $paginator->paginate(
            $sortedEvents ?? $events,
            $request->query->getInt('page', 1),
            $services->getSetting("events_per_page"),
            ['wrap-queries' => true]
        );

        $holidays = [
            "New Year's Day" => [
                "name" => "New Year's Day",
                "startDate" => "2023-01-01 00:00",
                "endDate" => "2023-01-01 00:00",
            ],
            "Independence Day" => [
                "name" => "Independence Day",
                "startDate" => "2023-07-04 00:00",
                "endDate" => "2023-07-04 00:00",
            ],
            "Thanksgiving Day" => [
                "name" => "Thanksgiving Day",
                "startDate" => "2023-11-23 00:00",
                "endDate" => "2023-11-23 00:00",
            ],
            "Martin Luther King Jr. Day" => [
                "name" => "Martin Luther King Jr. Day",
                "startDate" => "2023-01-16 00:00",
                "endDate" => "2023-01-16 00:00",
            ],
            "Memorial Day" => [
                "name" => "Memorial Day",
                "startDate" => "2023-05-29 00:00",
                "endDate" => "2023-05-29 00:00",
            ],
            "Labor Day - US" => [
                "name" => "Labor Day - US",
                "startDate" => "2023-09-04 00:00",
                "endDate" => "2023-09-04 00:00",
            ],
            "Veterans Day" => [
                "name" => "Veterans Day",
                "startDate" => "2023-11-11 00:00",
                "endDate" => "2023-11-11 00:00",
            ],
            "Orthodox Easter Sunday" => [
                "name" => "Orthodox Easter Sunday",
                "startDate" => "2023-04-16 00:00",
                "endDate" => "2023-04-16 00:00",
            ],
            "Labor Day - GR" => [
                "name" => "Labor Day - GR",
                "startDate" => "2023-05-01 00:00",
                "endDate" => "2023-05-01 00:00",
            ],
            "Christmas Day" => [
                "name" => "Christmas Day",
                "startDate" => "2023-12-25 00:00",
                "endDate" => "2023-12-25 00:00",
            ],
            "Greek Independence Day" => [
                "name" => "Greek Independence Day",
                "startDate" => "2023-03-25 00:00",
                "endDate" => "2023-03-25 00:00",
            ],
            "Ohi Day" => [
                "name" => "Ohi Day",
                "startDate" => "2023-10-28 00:00",
                "endDate" => "2023-10-28 00:00",
            ],
        ];


        return $this->render('Front/Event/events.html.twig', [
            'events' => $eventsMergedPaginated,
            'holidays' => $holidays,
            'category' => $category,
            'tags' => $totaltags,
            'sortedby' => $sortedby,
            'out_of_campus' => $outofcampusonly,
            'help_events_only' => $help_events_only,
            "venue" => $venue,
            'open_events_only' => $open_events_only,
            'hide_repeating' => $hide_repeating,
            'todayEventCount' => $todayEventCount,
            'tomorrowEventCount' => $tomorrowEventCount,
            'thisWeekEventCount' => $thisWeekEventCount,
            'nextWeekEventCount' => $nextWeekEventCount,
            'thisMonthEventCount' => $thisMonthEventCount,
            'nextMonthEventCount' => $nextMonthEventCount,
        ]);
    }

    public function getDateRange($startDateModifier, $endDateModifier = null)
    {
        $now = new DateTime();
        $startDate = clone $now->modify($startDateModifier);

        // Check if an end date modifier is provided, otherwise set it to the end of today
        if ($endDateModifier === null) {
            $endDateModifier = 'today +1 day -1 second';
        }

        $endDate = clone $now->modify($endDateModifier);
        $endDate->setTime(23, 59, 59);

        return ['start' => $startDate, 'end' => $endDate];
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

    /**
     * Sort events by the count of attendees in ascending order (least attendees first).
     *
     * @param Event[] $events
     * @return Event[]
     */
    private function sortByLeastAttendees(array $events, $services): array
    {
        usort($events, function ($a, $b) use ($services) {
            return $services->getAttendeesBookedOfEvent($a->getSlug()) <=> $services->getAttendeesBookedOfEvent($b->getSlug());
        });

        return $events;
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
     * @Route("/event/management/files/download/{fileId}", name="dashboard_organizer_management_files_download_file_event")
     */
    public function download($fileId, EventFileRepository $eventFileRepository, ParameterBagInterface $parameterBag)
    {
        // Retrieve the File entity based on $fileId
        $file = $eventFileRepository->find($fileId);

        // Check if the file exists
        if (!$file) {
            throw $this->createNotFoundException('File not found');
        }
        // Get the absolute file path
        $filePath = $parameterBag->get('kernel.project_dir') . "/public/uploads/files/" . $file->getFileName();

        // Create a BinaryFileResponse for downloading the file
        $response = new BinaryFileResponse($filePath);

        // Set the file name for the download
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file->getFileName() // Replace with your actual method to get the file name
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    public function getAbsolutePath(File $file)
    {
        return $this->uploaderHelper->asset($file, 'fileFile');
    }

    /**
     * @Route("/event/{slug}", name="event")
     */
    public function event($slug, OrderRepository $orderRepository, AppServices $services, ListingService $listingService): Response
    {

        $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all"))[0];

        if (!$event) {
            $this->addFlash('error', 'The event could not be found');
            return $this->redirectToRoute('events');
        }
        $event->viewed();
        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();


        // check if the user has already booked a ticket
        $user = $this->getUser();

        $orders = $orderRepository->getOrdersByUser($user);
        $eventDates = [];

// Iterate through the user's orders and order elements
        foreach ($orders as $order) {
            $orderElements = $order->getOrderelements();

            foreach ($orderElements as $orderElement) {
                // Get the event date associated with the order element
                $eventDate = $orderElement->getEventticket()->getEventdate();

                // Add the event date to the array if it's not already included
                if (!in_array($eventDate, $eventDates, true)) {
                    $eventDates[] = $eventDate;
                }
            }
        }


        foreach ($eventDates as $eventDate) {
            if ($eventDate->getTickets()[0]->getActive()) {
                $eventDate->getTickets()[0]->setAlreadybooked(true);
            }
        }

        $listings = $listingService->getListingsForEvent($event, null);

        return $this->render('Front/Event/event.html.twig', ["event" => $event, "listings" => $listings]);
    }

    /**
     * @Route("/open-event/{slug}", name="open_event")
     */
    public function openevent($slug, OrderElementRepository $orderElementRepository, OrderRepository $orderRepository, AppServices $services, EventTicketRepository $eventTicketRepository): Response
    {
        $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all"))[0];


        if (!$event) {
            $this->addFlash('error', 'The event could not be found');
            return $this->redirectToRoute('events');
        }

        if ($event->getOpen() == 1 or $event->getOpen() == true) {
            $event->viewed();
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();


            // check if the user has already booked a ticket
            $user = $this->getUser();

            $orders = $orderRepository->getOrdersByUser($user);
            $eventDates = [];

// Iterate through the user's orders and order elements
            foreach ($orders as $order) {
                $orderElements = $order->getOrderelements();

                foreach ($orderElements as $orderElement) {
                    // Get the event date associated with the order element
                    $eventDate = $orderElement->getEventticket()->getEventdate();

                    // Add the event date to the array if it's not already included
                    if (!in_array($eventDate, $eventDates, true)) {
                        $eventDates[] = $eventDate;
                    }
                }
            }


            foreach ($eventDates as $eventDate) {
                if ($eventDate->getTickets()[0]->getActive()) {
                    $eventDate->getTickets()[0]->setAlreadybooked(true);
                }
            }
        } else {
            return $this->redirectToRoute('fos_user_security_login');
        }


        return $this->render('Front/Event/open-event.html.twig', ["event" => $event]);
    }

    /**
     * @Route("/categories", name="categories")
     */
    public function categories(): Response
    {
        return $this->render('Front/Event/categories.html.twig');
    }

    /**
     * @Route("/open-events", name="open_events")
     * @throws InvalidDatabaseException|InvalidArgumentException
     */
    public function openevents(Request $request, EventRepository $eventRepository, PaginatorInterface $paginator, AppServices $services): Response
    {
        // Get the current date and time
        $now = new DateTime();

// Set the initial time to midnight (00:00:00) and max time to 23:59:59
        $todayStart = clone $now->setTime(0, 0, 0);
        $todayEnd = clone $now->setTime(23, 59, 59);

// Move to tomorrow
        $tomorrow = $now->modify('+1 day');
// Set the initial time to midnight (00:00:00) and max time to 23:59:59
        $tomorrowStart = clone $tomorrow->setTime(0, 0, 0);
        $tomorrowEnd = clone $tomorrow->setTime(23, 59, 59);

// Start of this week (assuming Monday is the start of the week)
        $thisWeekStartDate = clone (new DateTime())->modify('previous Monday');
// End of this week (assuming Sunday is the end of the week)
        $thisWeekEndDate = clone (new DateTime())->modify('next Sunday')->setTime(23, 59, 59);

// Start of next week
        $nextWeekStartDate = clone (new DateTime())->modify('next Monday')->modify('+7 days');
// End of next week
        $nextWeekEndDate = clone (new DateTime())->modify('next Sunday')->modify('+7 days')->setTime(23, 59, 59);

// Start of this month
        $thisMonthStartDate = clone (new DateTime())->modify('first day of this month');
// End of this month
        $thisMonthEndDate = clone (new DateTime())->modify('last day of this month')->setTime(23, 59, 59);

// Start of next month
        $nextMonthStartDate = clone (new DateTime())->modify('first day of next month');
// End of next month
        $nextMonthEndDate = clone (new DateTime())->modify('last day of next month')->setTime(23, 59, 59);

// Get event counts for the date ranges
        $todayEventCount = $services->getEventCountForDateRangeOpen($todayStart, $todayEnd);
        $tomorrowEventCount = $services->getEventCountForDateRangeOpen($tomorrowStart, $tomorrowEnd);
        $thisWeekEventCount = $services->getEventCountForDateRangeOpen($thisWeekStartDate, $thisWeekEndDate);
        $nextWeekEventCount = $services->getEventCountForDateRangeOpen($nextWeekStartDate, $nextWeekEndDate);
        $thisMonthEventCount = $services->getEventCountForDateRangeOpen($thisMonthStartDate, $thisMonthEndDate);
        $nextMonthEventCount = $services->getEventCountForDateRangeOpen($nextMonthStartDate, $nextMonthEndDate);

        $startDateMin = clone $tomorrow;
        $startDateMin->setTime(0, 0, 0); // Set to midnight
        $startDateMax = clone $tomorrow;
        $startDateMax->setTime(23, 59, 59); // Set to end of the day

        $category = $request->query->get('category') ?: "all";
        $keyword = $request->query->get('keyword') == "" ? "all" : $request->query->get('keyword');
        $organizer = $request->query->get('organizer') == "" ? "all" : $request->query->get('organizer');
        $selectedStartDate = $request->query->get('startdate') == "" ? "all" : $request->query->get('startdate');

        if ($category != "all") {
            $category = $services->getCategories(array("slug" => $category))->getQuery()->getOneOrNullresult();
            if (!$category) {
                $this->addFlash('error', 'The category can not be found');
                return $this->redirectToRoute('events');
            }
        }

        if ($selectedStartDate !== "all") {
            $selectedStartDate = $this->convertStartDateStringToDateTime($startdate);
        } else {
            $selectedStartDate = null; // Handle the "all" case as needed
        }

// Fetch upcoming events
        $eventsUpcoming = $services->getEventsHomepageIndex([
            "elapsed" => false,
            "open" => true,
            "category" => $category,
            'keyword' => $keyword,
            'published' => true,
            'startdate' => $selectedStartDate,
            'organizer' => $organizer,
        ])->getQuery()->getResult();
// Fetch old events
        $eventsOld = $services->getEventsHomepageIndex([
            "elapsed" => true,
            "open" => true,
            'published' => true,
            "category" => $category,
            'keyword' => $keyword,
            'startdate' => $selectedStartDate,
            'organizer' => $organizer,
        ])->getQuery()->getResult();
        foreach ($eventsOld as $event) {
            $event->setIsPassed(true);
        }

// Iterate through attended events and add them to the merged array
        $mergedEvents = [];

// Iterate through upcoming events and add them to the merged array
        foreach ($eventsUpcoming as $event) {
            $mergedEvents[$event->getId()] = $event;
        }

// Iterate through old events and add them to the merged array
        foreach ($eventsOld as $event) {
            $mergedEvents[$event->getId()] = $event;
        }

// Sort the merged events by startdate (ascending order)
        usort($mergedEvents, static function ($a, $b) {
            return $b->getEventdates()[0]->getStartdate() <=> $a->getEventdates()[0]->getStartdate();
        });
// Filter out events that are past the selected start date
        if ($selectedStartDate !== null) {
            $filteredEvents = array_filter($mergedEvents, function ($event) use ($selectedStartDate) {
                $eventStartDate = $event->getEventdates()[0]->getStartdate();
                return $eventStartDate >= $selectedStartDate;
            });

            // Assign the filtered events back to $mergedEvents
            $mergedEvents = $filteredEvents;
        }

// Paginate the sorted merged events
        $eventsMergedPaginated = $paginator->paginate(
            $mergedEvents,
            $request->query->getInt('page', 1),
            $services->getSetting("events_per_page"),
            ['wrap-queries' => true]
        );

        return $this->render('Front/Event/open-events.html.twig', [
            'events' => $eventsMergedPaginated,
            'category' => $category,
            'todayEventCount' => $todayEventCount,
            'tomorrowEventCount' => $tomorrowEventCount,
            'thisWeekEventCount' => $thisWeekEventCount,
            'nextWeekEventCount' => $nextWeekEventCount,
            'thisMonthEventCount' => $thisMonthEventCount,
            'nextMonthEventCount' => $nextMonthEventCount,
        ]);
    }

    public function convertStartDateStringToDateTime($startdate)
    {
        $now = new DateTime();

        switch ($startdate) {
            case "today":
                return $now->setTime(0, 0, 0);
            case "tomorrow":
                return $now->modify('+1 day')->setTime(0, 0, 0);
            case "thisweekend":
                return (new DateTime('saturday this week'))->setTime(0, 0, 0);
            case "thisweek":
                return (new DateTime('monday this week'))->setTime(0, 0, 0);
            case "nextweek":
                return (new DateTime('monday next week'))->setTime(0, 0, 0);
            case "thismonth":
                return (new DateTime('first day of this month'))->setTime(0, 0, 0);
            case "nextmonth":
                return (new DateTime('first day of next month'))->setTime(0, 0, 0);
            case "pickadate":
                // Handle custom date selection logic here if needed
                $customDate = $criterias['custom_date'] ?? null;
                if ($customDate) {
                    return (new DateTime($customDate))->setTime(0, 0, 0);
                }
            default:
                throw new \InvalidArgumentException("Invalid startdate value: " . $startdate);
        }
    }
}
