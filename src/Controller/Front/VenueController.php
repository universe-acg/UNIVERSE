<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use App\Service\VenueService;
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
        $venues = $paginator->paginate($services->getVenues(array('keyword' => $keyword)), $request->query->getInt('page', 1));
        return $this->render('Front/Venue/venues.html.twig', [
            'venues' => $venues
        ]);
    }

    /**
     * @Route("/venue/{slug}", name="venue")
     */
    public function venueBySlug($slug, AppServices $services): Response
    {
        // Retrieve the venue by slug from the service or repository
        $venue = $this->venueService->getVenueBySlug($slug);

        // Check if the venue exists
        if (!$venue) {
            throw $this->createNotFoundException('Venue not found');
        }

        return $this->render('Dashboard/Shared/Venue/venue_details.html.twig', [
            'venue' => $venue,
        ]);
    }
}
