<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Attendee;

use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @Route("/dashboard/attendee/following/add/{slug}", name="app_dashboard_attendee_following_add", condition="request.isXmlHttpRequest()")
     * @Route("/dashboard/attendee/following/remove/{slug}", name="app_dashboard_attendee_following_remove", condition="request.isXmlHttpRequest()")
     */
    public function addRemove($slug, AppServices $services, Request $request, RequestStack $requestStack)
    {
        $user = $services->getUsers(array("organizerslug" => $slug))->getQuery()->getOneOrNullResult();

        if (!$user) {
            return new JsonResponse(['error' => 'The organizer cannot be found']);
        }

        $em = $this->getDoctrine()->getManager();

        if ($user->getOrganizer()->isFollowedBy($this->getUser())) {
            $user->getOrganizer()->removeFollowedby($this->getUser());
        } else {
            $user->getOrganizer()->addFollowedby($this->getUser());
        }

        $em->persist($user->getOrganizer());
        $em->flush();

        // Define the response data based on the outcome of the operation
        $responseData = [];

        if ($user->getOrganizer()->isFollowedBy($this->getUser())) {
            $responseData['success'] = 'You are now following this organizer.';
        } else {
            $responseData['success'] = 'You have unfollowed this organizer.';
        }

        // Return a JsonResponse with the $responseData
        return new JsonResponse($responseData);
    }
}
