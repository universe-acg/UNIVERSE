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
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GoverningBodyController extends Controller
{

    /**
     *
     */
#[Route("/managementgb/delete", name: "managementgb_delete_xml", methods: ["POST"])]
    public function deleteGbMemberById(Request $request, UniverseGbMemberService $universeGbMemberService)
    {
        $id = $request->request->get('id');
        $this->assertOwnMember($id);
        $universeGbMemberService->deleteById($id);
        $responseData['success'] = 'Successfully deleted GB member';
        return new JsonResponse($responseData, 200);
    }

    /**
     *
     */
#[Route("/managementgb", name: "managementgb", methods: ["GET"])]
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
     *
     */
#[Route("/managementgb/update", name: "managementgb_update_xml", methods: ["POST"])]
    public function updateGbMembersByOrg(UserService $userService, Request $request, UniverseGbMemberService $universeGbMemberService)
    {
        $jsonData = json_decode($request->getContent(), true);

        if (
            !isset($jsonData['id']) ||
            !isset($jsonData['title']) ||
            !isset($jsonData['description']) ||
            !isset($jsonData['user'])
        ) {

            $responseData = ['error' => 'Some required fields are missing.'];
            return new JsonResponse($responseData, 400); // Use an appropriate HTTP status code, e.g., 400 Bad Request
        }

        $id = $jsonData['id'];
        $this->assertOwnMember($id);
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
     *
     */
#[Route("/managementgb/add", name: "managementgb_add", methods: ["POST"])]
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










































































































































































































































































    private function assertOwnMember($id): void {
        $record=$this->getDoctrine()->getRepository(UniverseGbMember::class)->find($id);
        if(!$record||$record->getOrg()?->getId()!==$this->getUser()?->getOrganizer()?->getId())throw $this->createAccessDeniedException();
    }
}
