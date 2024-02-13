<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Attendee;

use App\Service\AppServices;
use App\Service\UserService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FollowingController extends Controller
{

    /**
     * @Route("/following", name="dashboard_attendee_following")
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services)
    {
        $users = $paginator->paginate($services->getUsers(array("followedby" => $this->getUser()))->getQuery(), $request->query->getInt('page', 1), 12, array('wrap-queries' => true));
        return $this->render('Dashboard/Attendee/following.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/dashboard/attendee/following/add/{id}", name="app_dashboard_attendee_following_add", condition="request.isXmlHttpRequest()")
     * @Route("/dashboard/attendee/following/remove/{id}", name="app_dashboard_attendee_following_remove", condition="request.isXmlHttpRequest()")
     */
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

        // Define the response data based on the outcome of the operation
        $responseData = [];

        if ($user->isFollowedBy($this->getUser())) {
            $responseData['success'] = 'You are now following this organizer';
        } else {
            $responseData['success'] = 'You have unfollowed this organizer';
        }

        // Return a JsonResponse with the $responseData
        return new JsonResponse($responseData);
    }

    /**
     *
     * @Route("/dashboard/attendee/following/attendee/add/{id}", name="app_dashboard_attendee_following_add_attendee", condition="request.isXmlHttpRequest()")
     * @Route("/dashboard/attendee/following/attendee/remove/{id}", name="app_dashboard_attendee_following_remove_attendee", condition="request.isXmlHttpRequest()")
     */
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

        // Define the response data based on the outcome of the operation
        $responseData = [];

        if ($user->isFollowedBy($this->getUser())) {
            $responseData['success'] = 'You are now following this user';
        } else {
            $responseData['success'] = 'You have unfollowed this user';
        }

        // Return a JsonResponse with the $responseData
        return new JsonResponse($responseData);
    }
}
