<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Front;
    
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
        /**
         * @Route("/events", name="events")
         * @throws InvalidDatabaseException|InvalidArgumentException
         */
        public function events(Request $request, PaginatorInterface $paginator, AppServices $services): Response
        {
            $today = new DateTime();
            $tomorrow = (new DateTime())->modify('+1 day'); // Tomorrow
            $thisWeekStartDate = (new DateTime())->modify('previous Monday'); // Start of this week (assuming Monday is the start of the week)
            $thisWeekEndDate = (new DateTime())->modify('next Sunday'); // End of this week (assuming Sunday is the end of the week)
            $nextWeekStartDate = (new DateTime())->modify('next Monday')->modify('+7 days'); // Start of next week
            $nextWeekEndDate = (new DateTime())->modify('next Sunday')->modify('+7 days'); // End of next week
            $thisMonthStartDate = (new DateTime())->modify('first day of this month'); // Start of this month
            $thisMonthEndDate = (new DateTime())->modify('last day of this month'); // End of this month
            $nextMonthStartDate = (new DateTime())->modify('first day of next month'); // Start of next month
            $nextMonthEndDate = (new DateTime())->modify('last day of next month'); // End of next month
            
            $todayEventCount = $services->getEventCountForDateRange($today, $tomorrow);
            $tomorrowEventCount = $services->getEventCountForDateRange($tomorrow, $tomorrow);
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
            $localonly = $request->query->get('localonly') == "" ? "all" : $request->query->get('localonly');
            $country = $request->query->get('country') == "" ? "all" : $request->query->get('country');
            $location = $request->query->get('location') == "" ? "all" : $request->query->get('location');
            $startdate = $request->query->get('startdate') == "" ? "all" : $request->query->get('startdate');
            $freeonly = $request->query->get('freeonly') == "" ? "all" : $request->query->get('freeonly');
            $pricemin = $request->query->get('pricemin') == "" || $request->query->get('pricemin') == "0" ? "all" : $request->query->get('pricemin');
            $pricemax = $request->query->get('pricemax') == "" || $request->query->get('pricemax') == "10000" ? "all" : $request->query->get('pricemax');
            $audience = $request->query->get('audience') == "" ? "all" : $request->query->get('audience');
            $organizer = $request->query->get('organizer') == "" ? "all" : $request->query->get('organizer');
            $onlineonly = $request->query->get('onlineonly') == "" ? "all" : $request->query->get('onlineonly');
            
            if ($category != "all") {
                $category = $services->getCategories(array("slug" => $category))->getQuery()->getOneOrNullresult();
                if (!$category) {
                    $this->addFlash('error', 'The category can not be found');
                    return $this->redirectToRoute('events');
                }
            }
            
            $events = $paginator->paginate($services->getEvents(array("category" => $category, 'keyword' => $keyword, 'localonly' => $localonly, 'country' => $country, 'location' => $location, 'startdate' => $startdate, 'pricemin' => $pricemin, 'pricemax' => $pricemax, 'audience' => $audience, 'organizer' => $organizer, 'freeonly' => $freeonly, 'onlineonly' => $onlineonly))->getQuery(), $request->query->getInt('page', 1), $services->getSetting("events_per_page"), array('wrap-queries' => true));
            
            return $this->render('Front/Event/events.html.twig', [
                'events' => $events,
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
        public function event($slug, AppServices $services): Response
        {
            
            if ($this->isGranted("ROLE_ADMINISTRATOR")) {
                $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all", "published" => "all"))->getQuery()->getOneOrNullResult();
            } elseif ($this->isGranted("ROLE_ORGANIZER")) {
//            $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all", "published" => "all"))->getQuery()->getOneOrNullResult();
                $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all"))->getQuery()->getOneOrNullResult();
                if ($event && $event->getOrganizer() != $this->getUser()->getOrganizer() && !$event->getPublished()) {
                    $event = null;
                }
            } else {
                $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all"))->getQuery()->getOneOrNullResult();
            }
            
            if (!$event) {
                $this->addFlash('error', 'The event could not be found');
                return $this->redirectToRoute('events');
            }
            $event->viewed();
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            
            return $this->render('Front/Event/event.html.twig', ["event" => $event]);
        }
        
        /**
         * @Route("/categories", name="categories")
         */
        public function categories(): Response
        {
            return $this->render('Front/Event/categories.html.twig');
        }
    }
