<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Dashboard\Attendee;
    
    use App\Repository\EventRepository;
    use App\Service\AppServices;
    use Doctrine\ORM\NonUniqueResultException;
    use Exception;
    use Knp\Component\Pager\PaginatorInterface;
    use MaxMind\Db\Reader\InvalidDatabaseException;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    
    class FavoritesController extends Controller
    {
        
        /**
         * @Route("/my-favorites", name="favorites")
         * @throws Exception
         */
        public function index(Request $request, PaginatorInterface $paginator, EventRepository $eventRepository, AppServices $services)
        {
            
            // Get the current user
            $user = $this->getUser();
            
            // Get favorite events as QueryBuilder
            $favoriteEventsQueryBuilder = $services->getEvents(array("addedtofavoritesby" => $this->getUser()));
            
            // Get attended events as QueryBuilder
            $attendedEventsQueryBuilder = $eventRepository->findAttendedEventsByUser($user);
            
            // Convert QueryBuilder results to arrays
            $favoriteEvents = $favoriteEventsQueryBuilder->getQuery()->getResult();
            $attendedEvents = $attendedEventsQueryBuilder->getQuery()->getResult();

// Create an associative array to store favorite events by ID
            $favoriteEventsById = [];

// Populate the array with favorite events indexed by ID
            foreach ($favoriteEvents as $event) {
                $favoriteEventsById[$event->getId()] = $event;
            }

// Merge attended events with favorites, avoiding duplicates
            $mergedEvents = $favoriteEventsById; // Start with favorite events
            
            foreach ($attendedEvents as $event) {
                $eventId = $event->getId();
                if (isset($favoriteEventsById[$eventId])) {
                    // If the event exists in favorites, add it and mark as attended
                    $event->setIsAttnded(true);
                    $mergedEvents[$eventId] = $event;
                }
            }
            // Paginate the merged events
            $pagination = $paginator->paginate(
                $mergedEvents, // Merged array of events
                $request->query->getInt('page', 1), // Current page number
                12 // Number of items per page
            );
            
            return $this->render('Dashboard/Attendee/favorites.html.twig', [
                'events' => $pagination,
            ]);
        }
        
        //, condition="request.isXmlHttpRequest()"
        
        /**
         * @Route("/my-favorites/add/{slug}", name="favorites_add", condition="request.isXmlHttpRequest()")
         * @Route("/my-favorites/remove/{slug}", name="favorites_remove", condition="request.isXmlHttpRequest()")
         * @throws InvalidDatabaseException|NonUniqueResultException
         */
        public function addRemove($slug, AppServices $services)
        {
            
            $event = $services->getEvents(array("slug" => $slug))->getQuery()->getOneOrNullResult();
            if (!$event) {
                return new JsonResponse(['error' => 'The event can not be found']);
            }
            $em = $this->getDoctrine()->getManager();
            if ($event->isAddedToFavoritesBy($this->getUser())) {
                $this->getUser()->removeFavorite($event);
                $em->persist($this->getUser());
                $em->flush();
                return new JsonResponse(['success' => 'The event has been removed from your favorites']);
            }
            
            $this->getUser()->addFavorite($event);
            $em->persist($this->getUser());
            $em->flush();
            return new JsonResponse(['success' => 'The event has been added to your favorites']);
        }
    }
