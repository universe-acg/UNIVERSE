<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

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
        $associations = $managementClubsSocietiesService->findAllAssociationRequestsForClub($this->getUser()->getOrganizer()->getId());

        $associations = $paginator->paginate(
            $associations,
            $request->query->getInt('page', 1),
            10,
            ['wrap-queries' => true]
        );

        foreach ($associations as $associtaion) {
            $associtaion->setIsinitiatedbyme($managementClubsSocietiesService->isinitiatedbyme($associtaion->getId(), $associtaion->getUser()->getId()));
        }

        return $this->render('Dashboard/Organizer/ManagementClubsSocieties/index.html.twig', [
            'associations' => $associations,
            'managementClubsSocietiesService' => $managementClubsSocietiesService
        ]);
    }

    /**
     *
     */
#[Route("/management/add/{organizerid}/{attendeeid}", name: "app_dashboard_organizer_join_add", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function add(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
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
            $managementrow->setInitiatedBy($organizer->getUser());
            $managementrow->setStatus(0);

            $em->persist($managementrow);
            $em->flush();

            $responseData = [];
            $responseData['success'] = 'Congratulations, this user is now a pending member';
            return new JsonResponse($responseData);
        }

        $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);

        if ($row->getStatus() == 0 || $row->getStatus() == -1) {
            $em = $this->getDoctrine()->getManager();
            $row->setStatus(0);

            $em->persist($row);
            $em->flush();

            $responseData = [];
            $responseData['success'] = 'Congratulations, this user is now a pending member';
            return new JsonResponse($responseData);
        }

        $responseData['success'] = 'You are already member';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/management/accept/{organizerid}/{attendeeid}", name: "app_dashboard_organizer_join_accept", methods: ["POST"])]
    public function accept(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);
        if (!$user || !$organizer) {

            return $this->redirectToRoute("dashboard_organizer_management");
        }

        $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);

        if (!$row) {

            return $this->redirectToRoute("dashboard_organizer_management");
        }
        if ($row->getStatus() == 0 || $row->getStatus() == -1) {
            $em = $this->getDoctrine()->getManager();
            $row->setStatus(1);

            $em->persist($row);
            $em->flush();

            $this->addFlash('success', "Congratulations, this user is now a member");
            return $this->redirectToRoute("dashboard_organizer_management");
        }

        $this->addFlash('error', "Something went wrong. Contact admin");
        return $this->redirectToRoute("dashboard_organizer_management");
    }

    /**
     *
     */
#[Route("/management/acceptxml/{organizerid}/{attendeeid}", name: "app_dashboard_organizer_join_accept_xml", methods: ["POST"])]
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

            $responseData['success'] = 'Congratulations, this user is now a member';
            return new JsonResponse($responseData);
        }

        $responseData['success'] = 'Something went wrong. Contact admin';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/management/reject/{organizerid}/{attendeeid}", name: "app_dashboard_organizer_join_reject", methods: ["POST"])]
    public function reject(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            return new JsonResponse(['error' => 'Error']);
        }

        $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);

        if ($row->getStatus() == 0 || $row->getStatus() == -1) {
            $em = $this->getDoctrine()->getManager();
            $row->setStatus(-1);

            $em->persist($row);
            $em->flush();

            $responseData['success'] = 'Rejected their invitation to join';
            return new JsonResponse($responseData);
        }

        $responseData['success'] = 'Something went wrong. Contact admin';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/management/cancel/{organizerid}/{attendeeid}", name: "app_dashboard_organizer_join_cancel", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function cancelxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            return new JsonResponse(['error' => 'Error']);
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {
            $responseData['error'] = 'You cannot cancel a request that does not exist';
            return new JsonResponse($responseData);
        }

        $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($managementrow);
        $em->flush();

        $responseData['success'] = 'You have now canceled the request for them to join';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/dashboard/attendee/management/kick/{organizerid}/{attendeeid}", name: "app_dashboard_organizer_join_kick", methods: ["POST"])]
    public function kick(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {

            return $this->redirectToRoute("dashboard_organizer_management");
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {

            return $this->redirectToRoute("dashboard_organizer_management");
        }

        $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($managementrow);
        $em->flush();

        $this->addFlash('success', "They have now left the club");
        return $this->redirectToRoute("dashboard_organizer_management");
    }

    /**
     *
     */
#[Route("/dashboard/attendee/management/kickxml/{organizerid}/{attendeeid}", name: "app_dashboard_organizer_join_kick_xml", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function kickxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {
            return new JsonResponse(['error' => 'Error']);
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {
            $responseData = [];
            $responseData['error'] = 'You cannot kick them since they are not a member';
            return new JsonResponse($responseData);
        }

        $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($managementrow);
        $em->flush();

        $responseData = [];
        $responseData['success'] = 'They have now left the club';
        return new JsonResponse($responseData);
    }

    /**
     *
     */
#[Route("/management/cancelnotxml/{organizerid}/{attendeeid}", name: "app_dashboard_organizer_join_cancelnotxml", methods: ["POST"])]
    public function cancelnotxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
    {
        $user = $userService->getUserById($attendeeid);
        $organizer = $organizerService->getOrganizerById($organizerid);

        if (!$user || !$organizer) {

            return $this->redirectToRoute("dashboard_organizer_management");
        }

        $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);

        if ($isnew) {
            $this->addFlash('error', "You cannot cancel a request that does not exist");
            return $this->redirectToRoute("dashboard_organizer_management");
        }

        $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($managementrow);
        $em->flush();

        $this->addFlash('success', "You have now canceled the request for them to join");
        return $this->redirectToRoute("dashboard_organizer_management");
    }
}
