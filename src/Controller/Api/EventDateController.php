<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Api;

use App\Service\AppServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EventDateController extends Controller
{

    /**
     * @Route("/get-event-date/{reference}", name="get_eventdate")
     */
    public function getEventDate($reference = null, Request $request, AppServices $services): Response
    {
        if ($this->isGranted("ROLE_ATTENDEE") || !$this->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            throw new AccessDeniedHttpException();
        }
        $organizer = "all";
        if ($this->isGranted("ROLE_ORGANIZER")) {
            $organizer = $this->getUser()->getOrganizer()->getSlug();
        }
        $eventDate = $services->getEventDates(array("organizer" => $organizer, 'reference' => $reference))->getQuery()->getOneOrNullResult();
        if (!$eventDate) {
            return $this->json(array());
        }
        return $this->json(array("id" => $eventDate->getReference(), "text" => $eventDate->getEvent()->getName() . ' (' . date($this->getParameter("date_format_simple"), $eventDate->getStartdate()->getTimestamp()) . ')'));
    }

    /**
     * @Route("event/{eventSlug}/get-event-dates", name="get_eventdates_by_event")
     */
    public function getEventDates($eventSlug, Request $request, AppServices $services): Response
    {
        if ($this->isGranted("ROLE_ATTENDEE") || !$this->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            throw new AccessDeniedHttpException();
        }
        $organizer = "all";
        if ($this->isGranted("ROLE_ORGANIZER")) {
            $organizer = $this->getUser()->getOrganizer()->getSlug();
        }
        $limit = ($request->query->get('limit')) == "" ? 10 : $request->query->get('limit');

        $eventDates = $services->getEventDates(array("organizer" => $organizer, "event" => $eventSlug))->getQuery()->getResult();
        $results = array();
        foreach ($eventDates as $eventDate) {
            $result = array(
                'id' => $eventDate->getReference(),
                'text' => $eventDate->getEvent()->getName() . ' (' . date($this->getParameter("date_format_simple"), $eventDate->getStartdate()->getTimestamp()) . ')');
            array_push($results, $result);
        }
        return $this->json($results);
    }

}
