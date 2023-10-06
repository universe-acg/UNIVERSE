<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Front;
    
    use App\Repository\EventRepository;
    use App\Repository\EventTicketRepository;
    use App\Repository\OrderElementRepository;
    use App\Repository\OrderRepository;
    use App\Service\AppServices;
    use DateTime;
    use Knp\Component\Pager\PaginatorInterface;
    use MaxMind\Db\Reader\InvalidDatabaseException;
    use Psr\Cache\InvalidArgumentException;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
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
        public function events(Request $request, EventRepository $eventRepository, PaginatorInterface $paginator, AppServices $services): Response
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
            $todayEventCount = $services->getEventCountForDateRange($todayStart, $todayEnd);
            $tomorrowEventCount = $services->getEventCountForDateRange($tomorrowStart, $tomorrowEnd);
            $thisWeekEventCount = $services->getEventCountForDateRange($thisWeekStartDate, $thisWeekEndDate);
            $nextWeekEventCount = $services->getEventCountForDateRange($nextWeekStartDate, $nextWeekEndDate);
            $thisMonthEventCount = $services->getEventCountForDateRange($thisMonthStartDate, $thisMonthEndDate);
            $nextMonthEventCount = $services->getEventCountForDateRange($nextMonthStartDate, $nextMonthEndDate);
            
            $startDateMin = clone $tomorrow;
            $startDateMin->setTime(0, 0, 0); // Set to midnight
            $startDateMax = clone $tomorrow;
            $startDateMax->setTime(23, 59, 59); // Set to end of the day
            
            $category = $request->query->get('category') ?: "all";
            $keyword = $request->query->get('keyword') == "" ? "all" : $request->query->get('keyword');
            $organizer = $request->query->get('organizer') == "" ? "all" : $request->query->get('organizer');
            $startdate = $request->query->get('startdate') == "" ? "all" : $request->query->get('startdate');
            
            if ($category != "all") {
                $category = $services->getCategories(array("slug" => $category))->getQuery()->getOneOrNullresult();
                if (!$category) {
                    $this->addFlash('error', 'The category can not be found');
                    return $this->redirectToRoute('events');
                }
            }
            
            $user = $this->getUser();

// Fetch upcoming events
            $eventsUpcoming = $services->getEventsHomepageIndex([
                "elapsed" => false,
                "category" => $category,
                'keyword' => $keyword,
                'published' => true,
                'startdate' => $startdate,
                'organizer' => $organizer,
            ])->getQuery()->getResult();

// Fetch old events
            $eventsOld = $services->getEventsHomepageIndex([
                "elapsed" => true,
                'published' => true,
                "category" => $category,
                'keyword' => $keyword,
                'startdate' => $startdate,
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
            // Filter out events that are past the selected start date

// Filter out events where the start date is in the past

// Paginate the sorted merged events
            $eventsMergedPaginated = $paginator->paginate(
                $mergedEvents,
                $request->query->getInt('page', 1),
                $services->getSetting("events_per_page"),
                ['wrap-queries' => true]
            );
            
            return $this->render('Front/Event/events.html.twig', [
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
        
        /**
         * @Route("/event/{slug}", name="event")
         */
        public function event($slug, OrderRepository $orderRepository, AppServices $services): Response
        {
            
            $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all"))->getQuery()->getOneOrNullResult();
            
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
            
            return $this->render('Front/Event/event.html.twig', ["event" => $event]);
        }
        
        /**
         * @Route("/open-event/{slug}", name="open_event")
         */
        public function openevent($slug, OrderElementRepository $orderElementRepository, OrderRepository $orderRepository, AppServices $services, EventTicketRepository $eventTicketRepository): Response
        {
            $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all"))->getQuery()->getOneOrNullResult();
            
            
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
