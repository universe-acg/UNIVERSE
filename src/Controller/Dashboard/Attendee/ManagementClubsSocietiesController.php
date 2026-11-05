<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Attendee;

use App\Entity\ManagementClubsSocieties;
use App\Service\AppServices;
use App\Service\ManagementClubsSocietiesService;
use App\Service\OrganizerService;
use App\Service\UserService;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ManagementClubsSocietiesController extends Controller
{

    /**
     *
     */
#[Route("/management", name: "management", methods: ["GET"])]
    public function index(ManagementClubsSocietiesService $managementClubsSocietiesService, Request $request, PaginatorInterface $paginator, Security $security)
    {
        $associations = $managementClubsSocietiesService->findAllAssociationRequestsForUser($this->getUser()->getId());

        $associations = $paginator->paginate(
            $associations,
            $request->query->getInt('page', 1),
            10,
            ['wrap-queries' => true]
        );

        return $this->render('Dashboard/Attendee/ManagementClubsSocieties/index.html.twig', [
            'associations' => $associations,
            'managementClubsSocietiesService' => $managementClubsSocietiesService
        ]);
    }

    /**
     *
     */
#[Route("/management/add/{attendeeid}/{organizerid}", name: "app_dashboard_attendee_join_add", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function add(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            return new JsonResponse(['error' => 'Error']);
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {
            $em = $this->getDoctrine()->getManager();

            $managementrow = new ManagementClubsSocieties();
            $managementrow->setOrg($organizer);
            $managementrow->setUser($user);
            $managementrow->setInitiatedBy($user);
            $managementrow->setStatus(0);

            $em->persist($managementrow);
            $em->flush();

            $responseData = [];
            $responseData['success'] = 'Congratulations, you are now a pending member';
            return new JsonResponse($responseData);
        }

        $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        if ($row->getStatus() == 0 || $row->getStatus() == -1) {
            $em = $this->getDoctrine()->getManager();
            $row->setOrg($organizer);
            $row->setUser($user);
            $row->setInitiatedBy($user);
            $row->setStatus(0);

            $em->persist($row);
            $em->flush();

            $responseData = [];
            $responseData['success'] = 'Congratulations, you are now a pending member';
            return new JsonResponse($responseData);
        }

        $responseData['success'] = 'You are already member';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/management/accept/{attendeeid}/{organizerid}", name: "app_dashboard_attendee_join_accept", methods: ["POST"])]
    public function accept(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            $this->addFlash('error', "Something went wrong. Contact admin");
            return $this->redirectToRoute("dashboard_attendee_management");
        }

        $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);

        if ($row->getStatus() == 0 || $row->getStatus() == -1) {
            $em = $this->getDoctrine()->getManager();
            $row->setStatus(1);

            $em->persist($row);
            $em->flush();

            $this->addFlash('success', "Congratulations, you are now a member");
            return $this->redirectToRoute("dashboard_attendee_management");
        }

        $this->addFlash('error', "Something went wrong. Contact admin");
        return $this->redirectToRoute("dashboard_attendee_management");
    }

    /**
     *
     */
#[Route("/management/reject/{attendeeid}/{organizerid}", name: "app_dashboard_attendee_join_reject", methods: ["POST"])]
    public function reject(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            $responseData['error'] = 'Something went wrong. Contact admin';
            return new JsonResponse($responseData);
        }

        $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);

        if ($row->getStatus() == 0 || $row->getStatus() == -1) {
            $em = $this->getDoctrine()->getManager();
            $row->setStatus(-1);

            $em->persist($row);
            $em->flush();

            $responseData['success'] = 'Rejected the invitation to join';
            return new JsonResponse($responseData);
        }

        $responseData['error'] = 'Something went wrong. Contact admin';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/management/leave/{attendeeid}/{organizerid}", name: "app_dashboard_attendee_join_leave", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function leave(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            return new JsonResponse(['error' => 'Error']);
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {
            $responseData['success'] = 'You cannot leave since you are not a member';
            return new JsonResponse($responseData);
        }

        $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($managementrow);
        $em->flush();

        $responseData['success'] = 'You have now left the club';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/management/leavenonxml/{attendeeid}/{organizerid}", name: "app_dashboard_attendee_join_leave_nonxml", methods: ["POST"])]
    public function leavenonxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            $this->addFlash('error', "Error");
            return $this->redirectToRoute("dashboard_attendee_management");
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {
            $this->addFlash('error', "You cannot leave since you are not a member");
            return $this->redirectToRoute("dashboard_attendee_management");
        }

        $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($managementrow);
        $em->flush();

        $this->addFlash('success', "You have now left the club");
        return $this->redirectToRoute("dashboard_attendee_management");
    }

    /**
     *
     */
#[Route("/management/cancel/{attendeeid}/{organizerid}", name: "app_dashboard_attendee_join_cancel", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function cancelxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            return new JsonResponse(['error' => 'Error']);
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {
            $responseData['success'] = 'You cannot cancel a request that does not exist';
            return new JsonResponse($responseData);
        }

        $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($managementrow);
        $em->flush();

        $responseData['success'] = 'You have now canceled the request to join';
        return new JsonResponse($responseData);
    }


    /**
     *
     */
#[Route("/management/acceptxml/{attendeeid}/{organizerid}", name: "app_dashboard_attendee_join_accept_xml", methods: ["POST"])]
    public function acceptxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);
        if (!$user || !$organizer) {

            return $this->redirectToRoute("dashboard_organizer_management");
        }

        $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);

        if (!$row) {

            return new JsonResponse(['error' => 'Error']);
        }

        if ($row->getStatus() == 0 || $row->getStatus() == -1) {
            $em = $this->getDoctrine()->getManager();
            $row->setStatus(1);

            $em->persist($row);
            $em->flush();

            $responseData['success'] = 'Congratulations, you are now a member';
            return new JsonResponse($responseData);
        }

        $responseData['success'] = 'Something went wrong. Contact admin';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/management/cancelnotxml/{attendeeid}/{organizerid}", name: "app_dashboard_attendee_join_cancelnotxml", methods: ["POST"])]
    public function cancelnotxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            $this->addFlash('error', "Error");
            return $this->redirectToRoute("dashboard_attendee_management");
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {
            $this->addFlash('error', "You cannot cancel a request that does not exist");
            return $this->redirectToRoute("dashboard_attendee_management");
        }

        $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($managementrow);
        $em->flush();

        $this->addFlash('success', "You have now canceled the request to join");
        return $this->redirectToRoute("dashboard_attendee_management");
    }




































}
