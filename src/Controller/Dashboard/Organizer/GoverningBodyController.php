<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Dashboard\Organizer;
    
    use App\Entity\UniverseGbMember;
    use App\Service\ManagementClubsSocietiesService;
    use App\Service\UniverseGbMemberService;
    use App\Service\UserService;
    use Knp\Component\Pager\PaginatorInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    
    class GoverningBodyController extends Controller
    {
        
        /**
         * @Route("/dashboard/organizer/managementgb/delete", name="dashboard_organizer_managementgb_delete_xml")
         */
        public function deleteGbMemberById(Request $request, UniverseGbMemberService $universeGbMemberService)
        {
            $id = $request->request->get('id');
            $universeGbMemberService->deleteById($id);
            $responseData['success'] = 'Successfully deleted GB member';
            return new JsonResponse($responseData, 200);
        }
        
        /**
         * @Route("/dashboard/organizer/managementgb", name="dashboard_organizer_managementgb", methods="GET")
         */
        public function index(Request $request, PaginatorInterface $paginator, UniverseGbMemberService $universeGbMemberService, ManagementClubsSocietiesService $managementClubsSocietiesService)
        {
            $gbMembers = $universeGbMemberService->getByOrg($this->getUser()->getOrganizer()->getId());
            $members = $managementClubsSocietiesService->findMembersOfOrganization($this->getUser()->getOrganizer()->getId());
            
            return $this->render('Dashboard/Organizer/gbmembers/index.html.twig', [
                'gbMembers' => $gbMembers,
                'members' => $members,
                'managementClubsSocietiesService' => $universeGbMemberService
            ]);
        }
        
        /**
         * @Route("/dashboard/organizer/managementgb/update", name="dashboard_organizer_managementgb_update_xml", methods="POST")
         */
        public function updateGbMembersByOrg(UserService $userService, Request $request, UniverseGbMemberService $universeGbMemberService)
        {
            $jsonData = json_decode($request->getContent(), true);
            
            // Check if the required fields are present
            if (
                !isset($jsonData['id']) ||
                !isset($jsonData['title']) ||
                !isset($jsonData['description']) ||
                !isset($jsonData['user'])
            ) {
                // Return an error response (e.g., using Symfony's JsonResponse)
                $responseData = ['error' => 'Some required fields are missing.'];
                return new JsonResponse($responseData, 400); // Use an appropriate HTTP status code, e.g., 400 Bad Request
            }
            
            $id = $jsonData['id'];
            $title = $jsonData['title'];
            $description = $jsonData['description'];
            $selectedUserId = $jsonData['user'];
            
            $user = $userService->getUserById($selectedUserId);
            
            $newuniverseGbMember = $universeGbMemberService->getById($id)[0];
            
            $newuniverseGbMember->setOrg($this->getUser()->getOrganizer());
            $newuniverseGbMember->setDesc($description);
            $newuniverseGbMember->setAttendee($user);
            $newuniverseGbMember->setTitle($title);
            
            $universeGbMemberService->add($newuniverseGbMember);
            $responseData['success'] = 'Successfully updated new GB member';
            return new JsonResponse($responseData);
        }
        
        /**
         * @Route("/dashboard/organizer/managementgb/add", name="dashboard_organizer_managementgb_add", methods="POST")
         */
        public function addGbMembersByOrg(UserService $userService, Request $request, UniverseGbMemberService $universeGbMemberService)
        {
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $selectedUserId = $request->request->get('user');
            if ($selectedUserId == "all" || $selectedUserId == null || $selectedUserId == "") {
                $this->addFlash('error', "Please select a member for this role");
                return $this->redirectToRoute("dashboard_organizer_managementgb");
            }
            
            $user = $userService->getUserById($selectedUserId);
            $newuniverseGbMember = new UniverseGbMember();
            $newuniverseGbMember->setOrg($this->getUser()->getOrganizer());
            $newuniverseGbMember->setDesc($description);
            $newuniverseGbMember->setAttendee($user);
            $newuniverseGbMember->setTitle($title);
            
            $universeGbMemberService->add($newuniverseGbMember);
            
            $this->addFlash('success', "Successfully added new GB member");
            return $this->redirectToRoute("dashboard_organizer_managementgb");
        }

//
//        /**
//         * @Route("/dashboard/organizer/management/add/{organizerid}/{attendeeid}", name="app_dashboard_organizer_join_add", condition="request.isXmlHttpRequest()")
//         */
//        public function add(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
//        {
//            $user = $userService->getUserById($attendeeid);
//            $organizer = $organizerService->getOrganizerById($organizerid);
//
//            if (!$user || !$organizer) {
//                return new JsonResponse(['error' => 'Error']);
//            }
//
//            $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);
//
//            if ($isnew) {
//                $em = $this->getDoctrine()->getManager();
//
//                $managementrow = new ManagementClubsSocieties();
//                $managementrow->setOrg($organizer);
//                $managementrow->setUser($user);
//                $managementrow->setInitiatedBy($organizer->getUser());
//                $managementrow->setStatus(0);
//
//                $em->persist($managementrow);
//                $em->flush();
//
//                // Define the response data based on the outcome of the operation
//                $responseData = [];
//                $responseData['success'] = 'Congratulations, this user is now a pending member';
//                return new JsonResponse($responseData);
//            }
//
//            $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
//
//            if ($row->getStatus() == 0 || $row->getStatus() == -1) {
//                $em = $this->getDoctrine()->getManager();
//                $row->setStatus(0);
//
//                $em->persist($row);
//                $em->flush();
//
//                // Define the response data based on the outcome of the operation
//                $responseData = [];
//                $responseData['success'] = 'Congratulations, this user is now a pending member';
//                return new JsonResponse($responseData);
//            }
//
//            $responseData['success'] = 'You are already member';
//            return new JsonResponse($responseData);
//        }
//
//        /**
//         * @Route("/dashboard/organizer/management/accept/{organizerid}/{attendeeid}", name="app_dashboard_organizer_join_accept")
//         */
//        public function accept(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
//        {
//            $user = $userService->getUserById($attendeeid);
//            $organizer = $organizerService->getOrganizerById($organizerid);
//            if (!$user || !$organizer) {
////                $this->addFlash('error', "Something went wrong. Contact admin");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//
//            $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
//
//            if (!$row) {
////                $this->addFlash('error', "Something went wrong. Contact admin");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//            if ($row->getStatus() == 0 || $row->getStatus() == -1) {
//                $em = $this->getDoctrine()->getManager();
//                $row->setStatus(1);
//
//                $em->persist($row);
//                $em->flush();
//
//                // Define the response data based on the outcome of the operation
//                $this->addFlash('success', "Congratulations, this user is now a member");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//
//            $this->addFlash('error', "Something went wrong. Contact admin");
//            return $this->redirectToRoute("dashboard_organizer_management");
//        }
//
//        /**
//         * @Route("/dashboard/organizer/management/acceptxml/{organizerid}/{attendeeid}", name="app_dashboard_organizer_join_accept_xml")
//         */
//        public function acceptxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
//        {
//            $user = $userService->getUserById($attendeeid);
//            $organizer = $organizerService->getOrganizerById($organizerid);
//            if (!$user || !$organizer) {
////                $this->addFlash('error', "Something went wrong. Contact admin");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//
//            $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
//
//            if (!$row) {
////                $this->addFlash('error', "Something went wrong. Contact admin");
//                return new JsonResponse(['error' => 'Error']);
//            }
//
//            if ($row->getStatus() == 0 || $row->getStatus() == -1) {
//                $em = $this->getDoctrine()->getManager();
//                $row->setStatus(1);
//
//                $em->persist($row);
//                $em->flush();
//
//                // Define the response data based on the outcome of the operation
//                $responseData['success'] = 'Congratulations, this user is now a member';
//                return new JsonResponse($responseData);
//            }
//
//            $responseData['success'] = 'Something went wrong. Contact admin';
//            return new JsonResponse($responseData);
//        }
//
//        /**
//         * @Route("/dashboard/organizer/management/reject/{organizerid}/{attendeeid}", name="app_dashboard_organizer_join_reject")
//         */
//        public function reject(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
//        {
//            $user = $userService->getUserById($attendeeid);
//            $organizer = $organizerService->getOrganizerById($organizerid);
//
//            if (!$user || !$organizer) {
//                return new JsonResponse(['error' => 'Error']);
//            }
//
//            $row = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
//
//            if ($row->getStatus() == 0 || $row->getStatus() == -1) {
//                $em = $this->getDoctrine()->getManager();
//                $row->setStatus(-1);
//
//                $em->persist($row);
//                $em->flush();
//
//                // Define the response data based on the outcome of the operation
//                $this->addFlash('success', "Rejected their invitation to join");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//
//            $this->addFlash('error', "Something went wrong. Contact admin");
//            return $this->redirectToRoute("dashboard_organizer_management");
//        }
//
//        /**
//         * @Route("/dashboard/organizer/management/cancel/{organizerid}/{attendeeid}", name="app_dashboard_organizer_join_cancel", condition="request.isXmlHttpRequest()")
//         */
//        public function cancelxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
//        {
//            $user = $userService->getUserById($attendeeid);
//            $organizer = $organizerService->getOrganizerById($organizerid);
//
//            if (!$user || !$organizer) {
//                return new JsonResponse(['error' => 'Error']);
//            }
//
//            $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);
//
//            if ($isnew) {
//                $responseData['error'] = 'You cannot cancel a request that does not exist';
//                return new JsonResponse($responseData);
//            }
//
//            $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($managementrow);
//            $em->flush();
//
//            $responseData['success'] = 'You have now canceled the request for them to join';
//            return new JsonResponse($responseData);
//        }
//
//        /**
//         * @Route("/dashboard/attendee/management/kick/{organizerid}/{attendeeid}", name="app_dashboard_organizer_join_kick")
//         */
//        public function kick(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
//        {
//            $user = $userService->getUserById($attendeeid);
//            $organizer = $organizerService->getOrganizerById($organizerid);
//
//            if (!$user || !$organizer) {
////                $this->addFlash('error', "Error");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//
//            $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);
//
//            if ($isnew) {
////                $this->addFlash('error', "You cannot kick them since they are not a member");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//
//            $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($managementrow);
//            $em->flush();
//
//            $this->addFlash('success', "They have now left the club");
//            return $this->redirectToRoute("dashboard_organizer_management");
//        }
//
//        /**
//         * @Route("/dashboard/attendee/management/kickxml/{organizerid}/{attendeeid}", name="app_dashboard_organizer_join_kick_xml", condition="request.isXmlHttpRequest()")
//         */
//        public function kickxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $attendeeid, $organizerid)
//        {
//            $user = $userService->getUserById($attendeeid);
//            $organizer = $organizerService->getOrganizerById($organizerid);
//
//            if (!$user || !$organizer) {
//                return new JsonResponse(['error' => 'Error']);
//            }
//
//            $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);
//
//            if ($isnew) {
//                $responseData = [];
//                $responseData['error'] = 'You cannot kick them since they are not a member';
//                return new JsonResponse($responseData);
//            }
//
//            $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($managementrow);
//            $em->flush();
//
//            $responseData = [];
//            $responseData['success'] = 'They have now left the club';
//            return new JsonResponse($responseData);
//        }
//
//        /**
//         * @Route("/dashboard/organizer/management/cancelnotxml/{organizerid}/{attendeeid}", name="app_dashboard_organizer_join_cancelnotxml")
//         */
//        public function cancelnotxml(ManagementClubsSocietiesService $managementClubsSocietiesService, OrganizerService $organizerService, UserService $userService, AppServices $services, $organizerid, $attendeeid)
//        {
//            $user = $userService->getUserById($attendeeid);
//            $organizer = $organizerService->getOrganizerById($organizerid);
//
//            if (!$user || !$organizer) {
////                $this->addFlash('error', "Error");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//
//            $isnew = !$managementClubsSocietiesService->doesRowExist($attendeeid, $organizerid);
//
//            if ($isnew) {
//                $this->addFlash('error', "You cannot cancel a request that does not exist");
//                return $this->redirectToRoute("dashboard_organizer_management");
//            }
//
//            $managementrow = $managementClubsSocietiesService->findRowByUserIdAndOrgId($attendeeid, $organizerid);
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($managementrow);
//            $em->flush();
//
//            $this->addFlash('success', "You have now canceled the request for them to join");
//            return $this->redirectToRoute("dashboard_organizer_management");
//        }
    }
