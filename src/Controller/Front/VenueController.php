<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VenueController extends AbstractController
{

    /**
     * @Route("/venues", name="venues")
     */
    public function venues(Request $request, PaginatorInterface $paginator, AppServices $services): Response
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
        $country = ($request->query->get('country')) == "" ? "all" : $request->query->get('country');
        $venuetypes = ($request->query->get('venuetypes')) == "" ? "all" : $request->query->get('venuetypes');
        $minseatedguests = ($request->query->get('minseatedguests')) == "" ? "all" : $request->query->get('minseatedguests');
        $maxseatedguests = ($request->query->get('maxseatedguests')) == "" ? "all" : $request->query->get('maxseatedguests');
        $minstandingguests = ($request->query->get('minstandingguests')) == "" ? "all" : $request->query->get('minstandingguests');
        $maxstandingguests = ($request->query->get('maxstandingguests')) == "" ? "all" : $request->query->get('maxstandingguests');
        $venues = $paginator->paginate($services->getVenues(array('directory' => true, 'keyword' => $keyword, 'country' => $country, 'venuetypes' => $venuetypes, 'minseatedguests' => $minseatedguests, 'maxseatedguests' => $maxseatedguests, 'minstandingguests' => $minstandingguests, 'maxstandingguests' => $maxstandingguests)), $request->query->getInt('page', 1), 8);
        return $this->render('Front/Venue/venues.html.twig', [
            'venues' => $venues
        ]);
    }
}
