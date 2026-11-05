<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Entity\VolunteerEvent;
use App\Service\EventService;
use App\Service\VolunteerService;
use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class VolunteerEventController extends AbstractController
{
    private $volunteerService;

    public function __construct(VolunteerService $volunteerService)
    {
        $this->volunteerService = $volunteerService;
    }




    /**
     *
     */
#[Route("/volunteer-events/{id}/details", name: "volunteer_event_see_details")]
    public function seeDetails(VolunteerEvent $volunteerEvent, int $id)
    {

        $user = $this->getUser();

        if ($user) {
            $vol = $this->volunteerService->findVolunteerEventById($id);

            if ($vol) {
                return $this->render('Dashboard/Organizer/Volunteer/seedetails.html.twig', [
                    'volunteer' => $vol,
                    'userid' => $vol->getUser()->getId(),
                ]);
            }

            return new JsonResponse(['error' => 'Voluteer not found or you do not have permission.'], 403);
        }

        return $this->redirectToRoute('fos_user_security_login'); // Redirect to the login page if the user is not authenticated.
    }

    /**
     *
     */
#[Route("/volunteer-events/user/{userid}/{eventid}", name: "volunteer_event_see_all_for_user")]
    public function seeAllForUser(int $userid, int $eventid)
    {

        $user = $this->getUser();

        if ($user) {
            $volreqs = $this->volunteerService->findVolunteersByEventIdAndUserId($eventid);
            return $this->render('Dashboard/Organizer/Volunteer/seealloforg.html.twig', [
                'volreqs' => $volreqs,
            ]);
        }

        return $this->redirectToRoute('fos_user_security_login'); // Redirect to the login page if the user is not authenticated.
    }

    /**
     *
     */
#[Route("/volunteer-events/all", name: "volunteer_event_see_all")]
    public function seeAll()
    {


    }

    /**
     *
     */
#[Route("/volunteer-events/{eventid}/request", name: "volunteer_event_make_request", methods: ["POST"])]
    public function makeRequest(Request $request, int $eventid, EventService $eventService)
    {
        $event = $eventService->getEventById($eventid);
        if (!$event) {
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        $volunteerEvent = $this->volunteerService->findVolunteersByEventIdAndUserId($eventid, $this->getUser()->getId());

        if ($volunteerEvent) {
            $this->addFlash('success', 'You have already made a request to volunteer');
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        if ($request->isMethod('POST')) {

            $content = $request->request->get('message'); // Get the message content
            $this->volunteerService->createVolunteerEvent(
                $this->getUser(),
                $event,
                $content
            );
        }
        $this->addFlash('success', 'Volunteer request made succesfully');

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     *
     */
#[Route("/volunteer-events", name: "volunteer_event_list")]
    public function list()
    {
        $volunteerEvents = $this->volunteerService->findAllVolunteerEvents();

        return $this->render('volunteer_event/list.html.twig', [
            'volunteerEvents' => $volunteerEvents,
        ]);
    }
}
