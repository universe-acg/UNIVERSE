<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Scanner;

use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AttendeesCheckInController extends Controller
{

    /**
     * @Route("/event-date/{reference}/attendees-check-in", name="event_date_attendees_check_in")
     */
    public function eventDateAttendees($reference, Request $request, PaginatorInterface $paginator, AppServices $services)
    {

        $eventDate = $services->getEventDates(array("reference" => $reference, "organizer" => $this->getUser()->getScanner()->getOrganizer()->getSlug()))->getQuery()->getOneOrNullResult();
        if (!$eventDate) {
            $this->addFlash('error', $translator->trans('The event date can not be found'));
            return $this->redirectToRoute('dashboard_index');
        }

        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
        $checkedin = ($request->query->get('checkedin')) == "" ? "all" : $request->query->get('checkedin');

        $tickets = $paginator->paginate($services->getOrderTickets(array("eventdate" => $reference, "keyword" => $keyword, "checkedin" => $checkedin))->getQuery(), $request->query->getInt('page', 1), 20, array('wrap-queries' => true));
        return $this->render('Dashboard/Scanner/AttendeesCheckIn/event-date-attendees.html.twig', [
            "eventDate" => $eventDate,
            "tickets" => $tickets
        ]);
    }

    /**
     * @Route("/event-date/{eventDateReference}/attendees-check-in/{ticketReference}/check-in", name="ticket_check_in")
     */
    public function ticketCheckIn($eventDateReference, $ticketReference, Request $request, AppServices $services)
    {

        $eventDate = $services->getEventDates(array("reference" => $eventDateReference, "organizer" => $this->getUser()->getScanner()->getOrganizer()->getSlug()))->getQuery()->getOneOrNullResult();
        if (!$eventDate) {
            $this->addFlash('error', $translator->trans('The event date can not be found'));
            return $this->redirectToRoute('dashboard_index');
        }
        $ticket = $services->getOrderTickets(array("keyword" => $ticketReference))->getQuery()->getOneOrNullResult();
        if ($ticket->getScanned()) {
            $this->addFlash('error', $translator->trans('The ticket has already been scanned'));
            return $services->redirectToReferer('index');
        }
        $ticket->setScanned(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);
        $em->flush();
        $this->addFlash('success', $translator->trans('The ticket has been successfully scanned'));
        return $services->redirectToReferer('index');
    }

}
