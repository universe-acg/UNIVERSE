<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Attendee;

use App\Service\AppServices;
use App\Service\UserService;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class FollowingController extends Controller
{

    /**
     *
     */
#[Route("/following", name: "following", methods: ["GET"])]
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services)
    {
        $users = $paginator->paginate($services->getUsers(array("followedby" => $this->getUser()))->getQuery(), $request->query->getInt('page', 1), 12, array('wrap-queries' => true));
        return $this->render('Dashboard/Attendee/following.html.twig', [
            'users' => $users
        ]);
    }

    /**
     *
     *
     */
#[Route("/following/add/{id}", name: "app_dashboard_attendee_following_add", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
#[Route("/following/remove/{id}", name: "app_dashboard_attendee_following_remove", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function addRemove($id, AppServices $services, UserService $userService)
    {
        $user = $userService->getUserById($id);

        if (!$user) {
            return new JsonResponse(['error' => 'The organizer cannot be found']);
        }

        $em = $this->getDoctrine()->getManager();

        if ($user->isFollowedBy($this->getUser())) {
            $user->removeFollowedby($this->getUser());
        } else {
            $user->addFollowedby($this->getUser());
        }

        $em->persist($user);
        $em->flush();

        $responseData = [];

        if ($user->isFollowedBy($this->getUser())) {
            $responseData['success'] = 'You are now following this organizer';
        } else {
            $responseData['success'] = 'You have unfollowed this organizer';
        }

        return new JsonResponse($responseData);
    }

    /**
     *
     *
     *
     */
#[Route("/following/attendee/add/{id}", name: "app_dashboard_attendee_following_add_attendee", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
#[Route("/following/attendee/remove/{id}", name: "app_dashboard_attendee_following_remove_attendee", condition: "request.isXmlHttpRequest()", methods: ["POST"])]
    public function addRemoveAttendee($id, AppServices $services, UserService $userService)
    {
        $user = $userService->getUserById($id);

        if (!$user) {
            return new JsonResponse(['error' => 'The user cannot be found']);
        }

        $em = $this->getDoctrine()->getManager();

        if ($user->isFollowedBy($this->getUser())) {
            $user->removeFollowedby($this->getUser());
        } else {
            $user->addFollowedby($this->getUser());
        }

        $em->persist($user);
        $em->flush();

        $responseData = [];

        if ($user->isFollowedBy($this->getUser())) {
            $responseData['success'] = 'You are now following this user';
        } else {
            $responseData['success'] = 'You have unfollowed this user';
        }

        return new JsonResponse($responseData);
    }
}
