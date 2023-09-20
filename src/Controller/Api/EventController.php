<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Api;
    
    use App\Repository\EventRepository;
    use App\Service\AppServices;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Asset\Packages;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Contracts\Translation\TranslatorInterface;
    
    class EventController extends Controller
    {
        
        /**
         * @Route("/get-event-details/{eventId}", name="get_event_details", methods={"GET"})
         */
        public function getEventDetails(Request $request, EventRepository $eventRepository, $eventId)
        {
            // Retrieve the event details based on the eventId
            $event = $eventRepository->find($eventId);
            
            if (!$event) {
                // Handle the case where the event is not found
                return new JsonResponse(['error' => 'Event not found'], 404);
            }
            
            // You can customize the data you want to send back to the client
            $responseData = [
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'email' => $event->getEmail(),
                'phonenumber' => $event->getPhonenumber(),
                'tags' => $event->getTags(),
                'volsneeded' => $event->isVolsneeded(),
                'audiences' => $event->getAudiences(),
                'showattendees' => $event->getShowattendees(),
            ];
            
            return new JsonResponse($responseData);
        }
        
        /**
         * @Route("/get-event/{slug}", name="get_event")
         */
        public function getEvent($slug = null, Request $request, AppServices $services): Response
        {
            $published = ($request->query->get('published')) == "" ? true : $request->query->get('published');
            $elapsed = ($request->query->get('elapsed')) == "" ? false : $request->query->get('elapsed');
            $organizerEnabled = true;
            if ($this->isGranted("ROLE_ADMINISTRATOR")) {
                $organizerEnabled = "all";
            }
            $event = $services->getEvents(array('slug' => $slug, 'published' => $published, 'elapsed' => $elapsed, 'organizerEnabled' => $organizerEnabled))->getQuery()->getOneOrNullResult();
            
            return $this->json(array("slug" => $event->getSlug(), "text" => $event->getName()));
        }
        
        /**
         * @Route("/get-events", name="get_events")
         */
        public function getEvents(Request $request, AppServices $services, Packages $assetsManager, TranslatorInterface $translator): Response
        {
            $q = ($request->query->get('q')) == "" ? "all" : $request->query->get('q');
            $organizer = ($request->query->get('organizer')) == "" ? "all" : $request->query->get('organizer');
            $published = ($request->query->get('published')) == "" ? true : $request->query->get('published');
            $elapsed = ($request->query->get('elapsed')) == "" ? false : $request->query->get('elapsed');
            $limit = ($request->query->get('limit')) == "" ? 10 : $request->query->get('limit');
            if ($q == "all") {
                $limit = 3;
            }
            $organizerEnabled = true;
            if ($this->isGranted("ROLE_ADMINISTRATOR")) {
                $organizerEnabled = "all";
            }
            
            $events = $services->getEvents(array("organizer" => $organizer, 'keyword' => $q, 'published' => $published, 'elapsed' => $elapsed, 'limit' => $limit, 'organizerEnabled' => $organizerEnabled))->getQuery()->getResult();
            
            $results = array();
            foreach ($events as $event) {
                if ((!"all" && !$event->hasAnEventDateOnSale())) {
                    continue;
                }
                $venue = $translator->trans("Online");
                if ($event->getFirstOnSaleEventDate() && $event->getFirstOnSaleEventDate()->getVenue()) {
                    $venue = $event->getFirstOnSaleEventDate()->getVenue()->getName() . ": " . $event->getFirstOnSaleEventDate()->getVenue()->stringifyAddress();
                }
                $date = $translator->trans("No active events");
                if ($event->getFirstOnSaleEventDate() && $event->getFirstOnSaleEventDate()->getStartdate()) {
                    $date = date($this->getParameter("date_format_simple"), $event->getFirstOnSaleEventDate()->getStartdate()->getTimestamp());
                }
                
                $result = array(
                    'id' => $event->getSlug(),
                    'text' => $event->getName(),
                    'image' => $assetsManager->getUrl($event->getImagePath()),
                    'link' => $this->generateUrl("event", ['slug' => $event->getSlug(), '_locale' => $request->getLocale()]),
                    'date' => $date,
                    'venue' => $venue);
                array_push($results, $result);
            }
            
            return $this->json($results);
        }
        
    }
