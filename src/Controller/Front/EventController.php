<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventController extends AbstractController
{

    /**
     * @Route("/events", name="events")
     * @throws InvalidDatabaseException
     */
//        public function events(Request $request, PaginatorInterface $paginator, AppServices $services): Response
    public function events(Request $request, PaginatorInterface $paginator, AppServices $services, TranslatorInterface $translator): Response
    {

//        $localony = $request->query->get('localonly') == "" ? "all" : $request->query->get('localonly');
//        $country = $request->query->get('country') == "" ? "all" : $request->query->get('country');
//        $location = $request->query->get('location') == "" ? "all" : $request->query->get('location');
//        $freeonly = $request->query->get('freeonly') == "" ? "all" : $request->query->get('freeonly');
//        $pricemin = $request->query->get('pricemin') == "" || $request->query->get('pricemin') == "0" ? "all" : $request->query->get('pricemin');
//        $pricemax = $request->query->get('pricemax') == "" || $request->query->get('pricemax') == "10000" ? "all" : $request->query->get('pricemax');
//        $organizer = $request->query->get('organizer') == "" ? "all" : $request->query->get('organizer');
//        $onlineonly = $request->query->get('onlineonly') == "" ? "all" : $request->query->get('onlineonly');
        $category = "all";
        $categorySlug = $request->query->get('category') == "" ? "all" : $request->query->get('category');
        $keyword = $request->query->get('keyword') == "" ? "all" : $request->query->get('keyword');
        $startdate = $request->query->get('startdate') == "" ? "all" : $request->query->get('startdate');
        $audience = $request->query->get('audience') == "" ? "all" : $request->query->get('audience');

        if ($categorySlug != "all") {
            $category = $services->getCategories(array("slug" => $categorySlug))->getQuery()->getOneOrNullresult();
            if (!$category) {
                $this->addFlash('error', $translator->trans('The category can not be found'));
                return $this->redirectToRoute('events');
            }
        }

        $events = $paginator->paginate($services->getEvents(array("category" => $categorySlug, 'keyword' => $keyword, 'startdate' => $startdate, 'audience' => $audience))->getQuery(), $request->query->getInt('page', 1), $services->getSetting("events_per_page"), array('wrap-queries' => true));

        return $this->render('Front/Event/events.html.twig', [
            'events' => $events,
            'category' => $category,
        ]);

//            $slug = ($request->query->get('slug')) == "" ? "all" : $request->query->get('slug');
//            $category = ($request->query->get('category')) == "" ? "all" : $request->query->get('category');
//            $venue = ($request->query->get('venue')) == "" ? "all" : $request->query->get('venue');
//            $elapsed = ($request->query->get('elapsed')) == "" ? "all" : $request->query->get('elapsed');
//            $published = ($request->query->get('published')) == "" ? "all" : $request->query->get('published');
//
//            $organizer = "all";
//
//            $events = $paginator->paginate($services->getEvents(array("slug" => $slug, "category" => $category, "venue" => $venue, "elapsed" => $elapsed, "published" => $published, "organizer" => $organizer, "sort" => "startdate", "organizerEnabled" => "all", "sort" => "e.createdAt", "order" => "DESC"))->getQuery(), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));
//
//            return $this->render('Front/Event/events.html.twig', [
//                'events' => $events,
//                'category' => $category,
//            ]);
    }

    /**
     * @Route("/event/{slug}", name="event")
     */
    public function event($slug, AppServices $services, TranslatorInterface $translator): Response
    {

        if ($this->isGranted("ROLE_ADMINISTRATOR")) {
            $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all", "published" => "all"))->getQuery()->getOneOrNullResult();
        } elseif ($this->isGranted("ROLE_ORGANIZER")) {
            $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all", "published" => "all"))->getQuery()->getOneOrNullResult();
            if ($event && $event->getOrganizer() != $this->getUser()->getOrganizer() && !$event->getPublished()) {
                $event = null;
            }
        } else {
            $event = $services->getEvents(array("slug" => $slug, "elapsed" => "all"))->getQuery()->getOneOrNullResult();
        }

        if (!$event) {
            $this->addFlash('error', $translator->trans('The event not be found'));
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
