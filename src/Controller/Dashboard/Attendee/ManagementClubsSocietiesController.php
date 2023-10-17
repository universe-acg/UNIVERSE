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
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Security;
    
    class ManagementClubsSocietiesController extends Controller
    {
        
        /**
         * @Route("/management", name="dashboard_attendee_management", methods="GET")
         */
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
         * @Route("/dashboard/attendee/management/add/{attendeeid}/{organizerid}", name="app_dashboard_attendee_join_add", condition="request.isXmlHttpRequest()")
         */
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
                
                // Define the response data based on the outcome of the operation
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
                
                // Define the response data based on the outcome of the operation
                $responseData = [];
                $responseData['success'] = 'Congratulations, you are now a pending member';
                return new JsonResponse($responseData);
            }
            
            $responseData['success'] = 'You are already member';
            return new JsonResponse($responseData);
        }
        
        /**
         * @Route("/dashboard/attendee/management/accept/{attendeeid}/{organizerid}", name="app_dashboard_attendee_join_accept")
         */
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
                
                // Define the response data based on the outcome of the operation
                $this->addFlash('success', "Congratulations, you are now a member");
                return $this->redirectToRoute("dashboard_attendee_management");
            }
            
            $this->addFlash('error', "Something went wrong. Contact admin");
            return $this->redirectToRoute("dashboard_attendee_management");
        }
        
        /**
         * @Route("/dashboard/attendee/management/reject/{attendeeid}/{organizerid}", name="app_dashboard_attendee_join_reject")
         */
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
                
                // Define the response data based on the outcome of the operation
                $responseData['success'] = 'Rejected the invitation to join';
                return new JsonResponse($responseData);
            }
            
            $responseData['error'] = 'Something went wrong. Contact admin';
            return new JsonResponse($responseData);
        }
        
        /**
         * @Route("/dashboard/attendee/management/leave/{attendeeid}/{organizerid}", name="app_dashboard_attendee_join_leave", condition="request.isXmlHttpRequest()")
         */
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
         * @Route("/dashboard/attendee/management/leavenonxml/{attendeeid}/{organizerid}", name="app_dashboard_attendee_join_leave_nonxml")
         */
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
         * @Route("/dashboard/attendee/management/cancel/{attendeeid}/{organizerid}", name="app_dashboard_attendee_join_cancel", condition="request.isXmlHttpRequest()")
         */
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
         * @Route("/dashboard/attendee/management/acceptxml/{attendeeid}/{organizerid}", name="app_dashboard_attendee_join_accept_xml")
         */
        public function acceptxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
        {
            $user = $userService->getUserById($attendeeid);
            $organizer = $organizerService->getOrganizerById($organizerid);
            if (!$user || !$organizer) {
//                $this->addFlash('error', "Something went wrong. Contact admin");
                return $this->redirectToRoute("dashboard_organizer_management");
            }
            
            $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
            
            if (!$row) {
//                $this->addFlash('error', "Something went wrong. Contact admin");
                return new JsonResponse(['error' => 'Error']);
            }
            
            if ($row->getStatus() == 0 || $row->getStatus() == -1) {
                $em = $this->getDoctrine()->getManager();
                $row->setStatus(1);
                
                $em->persist($row);
                $em->flush();
                
                // Define the response data based on the outcome of the operation
                $responseData['success'] = 'Congratulations, you are now a member';
                return new JsonResponse($responseData);
            }
            
            $responseData['success'] = 'Something went wrong. Contact admin';
            return new JsonResponse($responseData);
        }
        
        /**
         * @Route("/dashboard/attendee/management/cancelnotxml/{attendeeid}/{organizerid}", name="app_dashboard_attendee_join_cancelnotxml")
         */
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
//
//        /**
//         * @Route("/dashboard/attendee/following/add/{slug}", name="app_dashboard_attendee_following_add", condition="request.isXmlHttpRequest()")
//         * @Route("/dashboard/attendee/following/remove/{slug}", name="app_dashboard_attendee_following_remove", condition="request.isXmlHttpRequest()")
//         */
//        public function addRemove($slug, AppServices $services)
//        {
//            $user = $services->getUsers(array("organizerslug" => $slug))->getQuery()->getOneOrNullResult();
//
//            if (!$user) {
//                return new JsonResponse(['error' => 'The organizer cannot be found']);
//            }
//
//            $em = $this->getDoctrine()->getManager();
//
//            if ($user->getOrganizer()->isFollowedBy($this->getUser())) {
//                $user->getOrganizer()->removeFollowedby($this->getUser());
//            } else {
//                $user->getOrganizer()->addFollowedby($this->getUser());
//            }
//
//            $em->persist($user->getOrganizer());
//            $em->flush();
//
//            // Define the response data based on the outcome of the operation
//            $responseData = [];
//
//            if ($user->getOrganizer()->isFollowedBy($this->getUser())) {
//                $responseData['success'] = 'You are now following this organizer.';
//            } else {
//                $responseData['success'] = 'You have unfollowed this organizer.';
//            }
//
//            // Return a JsonResponse with the $responseData
//            return new JsonResponse($responseData);
//        }
    }
