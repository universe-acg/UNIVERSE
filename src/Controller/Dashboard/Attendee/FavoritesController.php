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
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class FavoritesController extends Controller
{

    /**
     *
     * @throws Exception
     */
#[Route("/my-favorites", name: "favorites", methods: ["GET"])]
    public function index(Request $request, PaginatorInterface $paginator, EventRepository $eventRepository, AppServices $services)
    {

        $user = $this->getUser();

        $favoriteEventsQueryBuilder = $services->getEventssql(array("addedtofavoritesby" => $this->getUser()));

        $attendedEventsQueryBuilder = $eventRepository->findAttendedEventsByUser($user, "all", "all");

        $favoriteEvents = $favoriteEventsQueryBuilder->getQuery()->getResult();
        $attendedEvents = $attendedEventsQueryBuilder->getQuery()->getResult();

        $favoriteEventsById = [];

        foreach ($favoriteEvents as $event) {
            $favoriteEventsById[$event->getId()] = $event;
        }

        $mergedEvents = $favoriteEventsById; // Start with favorite events

        foreach ($attendedEvents as $event) {
            $eventId = $event->getId();
            if (isset($favoriteEventsById[$eventId])) {

                $event->setIsAttnded(true);
                $mergedEvents[$eventId] = $event;
            }
        }

        $pagination = $paginator->paginate(
            $mergedEvents, // Merged array of events
            $request->query->getInt('page', 1), // Current page number
            12 // Number of items per page
        );

        return $this->render('Dashboard/Attendee/favorites.html.twig', [
            'events' => $pagination,
        ]);
    }


    /**
     *
     *
     * @throws InvalidDatabaseException|NonUniqueResultException|Exception
     */
#[Route("/my-favorites/add/{slug}", name: "favorites_add", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
#[Route("/my-favorites/remove/{slug}", name: "favorites_remove", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function addRemove($slug, AppServices $services)
    {

        $event = $services->getEvents(array("slug" => $slug))[0];
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
