<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Controller\Api;

    use App\Repository\EventRepository;
    use App\Service\AppServices;
    use App\Controller\BaseController as Controller;
    use Symfony\Component\Asset\Packages;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;

    class EventController extends Controller
    {

        /**
         *
         */
#[Route("/get-event-details/{eventId}", name: "get_event_details", methods: ["GET"])]
        public function getEventDetails(EventRepository $eventRepository, $eventId)
        {

            $event = $eventRepository->find($eventId);

            if (!$event) {

                return new JsonResponse(['error' => 'Event not found'], 404);
            }

            $responseData = [
                'name' => $event->getName(),
                'slug' => $event->getSlug(),
                'description' => $event->getDescription(),
                'email' => $event->getEmail(),
                'phonenumber' => $event->getPhonenumber(),
                'tags' => $event->getTags(),
                'volsneeded' => $event->isVolsneeded(),
                'showattendees' => $event->getShowattendees(),
            ];

            return new JsonResponse($responseData);
        }

        /**
         *
         */
#[Route("/get-event/{slug}", name: "get_event")]
        public function getEvent(Request $request, AppServices $services, $slug = null): Response
        {
            $published = ($request->query->get('published')) == "" ? true : $request->query->get('published');
            $elapsed = ($request->query->get('elapsed')) == "" ? false : $request->query->get('elapsed');
            $organizerEnabled = true;
            if ($this->isGranted("ROLE_ADMINISTRATOR")) {
                $organizerEnabled = "all";
            }
            $event = $services->getEvents(array('slug' => $slug, 'published' => $published, 'elapsed' => $elapsed, 'organizerEnabled' => $organizerEnabled))[0];

            return $this->json(array("slug" => $event->getSlug(), "text" => $event->getName()));
        }

        /**
         *
         */
#[Route("/get-events", name: "get_events")]
        public function getEvents(Request $request, AppServices $services, Packages $assetsManager): Response
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

            $events = $services->getEvents(array("organizer" => $organizer, 'keyword' => $q, 'published' => $published, 'elapsed' => $elapsed, 'limit' => $limit, 'organizerEnabled' => $organizerEnabled));

            $results = array();
            foreach ($events as $event) {
                if ((!"all" && !$event->hasAnEventDateOnSale())) {
                    continue;
                }
                $date = date($this->getParameter("date_format_simple"), $event->getFirstOnSaleEventDate()->getStartdate()->getTimestamp());

                $result = array(
                    'id' => $event->getSlug(),
                    'text' => $event->getName(),
                    'image' => $assetsManager->getUrl($event->getImagePath()),
                    'link' => $this->generateUrl("event", ['slug' => $event->getSlug()]),
                    'date' => $date);
                array_push($results, $result);
            }

            return $this->json($results);
        }

    }
