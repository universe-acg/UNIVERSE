<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends Controller
{

    /**
     * @Route("/administrator/reports", name="dashboard_administrator_reports", methods="GET")
     * @Route("/organizer/reports", name="dashboard_organizer_reports", methods="GET")
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services)
    {

        $reference = ($request->query->get('reference')) == "" ? "all" : $request->query->get('reference');
        $organizer = ($request->query->get('organizer')) == "" ? "all" : $request->query->get('organizer');
        $event = ($request->query->get('event')) == "" ? "all" : $request->query->get('event');

        if ($this->isGranted("ROLE_ORGANIZER")) {
            $organizer = $this->getUser()->getOrganizer()->getSlug();
        }

        $eventDates = $paginator->paginate($services->getEventDates(array('reference' => $reference, 'organizer' => $organizer, 'event' => $event)), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/Reports/index.html.twig', [
            'eventDates' => $eventDates,
        ]);
    }

}
