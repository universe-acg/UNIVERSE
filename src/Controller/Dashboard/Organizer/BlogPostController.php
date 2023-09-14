<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

use App\Entity\BlogPost;
use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class BlogPostController extends Controller
{

    /**
     * @Route("/manage-blog-posts-organizer", name="blog_post_organizer", methods="GET")
     */
    public function index(Request $request, PaginatorInterface $paginator, Security $security, AppServices $services)
    {
        // Get the current user (user object)
        $user = $security->getUser();

        if ($user) {
            $blogposts = $paginator->paginate(
                $this->getDoctrine()->getRepository(BlogPost::class)->findBy(['user' => $user]),
                $request->query->getInt('page', 1),
                10,
                ['wrap-queries' => true]
            );

            return $this->render('Dashboard/Organizer/BlogPost/index.html.twig', [
                'blogposts' => $blogposts,
            ]);
        }

        return $this->redirectToRoute("dashboard_index");
    }
}
