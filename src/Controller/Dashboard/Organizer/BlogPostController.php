<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

use App\Entity\BlogPost;
use App\Form\BlogPostType;
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

    /**
     * @Route("/manage-blog-posts/add", name="blog_post_add", methods="GET|POST")
     * @Route("/manage-blog-posts/{slug}/edit", name="blog_post_edit", methods="GET|POST")
     */
    public function addedit(Request $request, AppServices $services, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $blogpost = new BlogPost();
            $user = $this->getUser();
            $blogpost->setUser($user);
        } else {
            $blogpost = $services->getBlogPosts(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
            if (!$blogpost) {
                $this->addFlash('error', 'The blog post can not be found');
                return $services->redirectToReferer('blog_post');
            }
        }

        $form = $this->createForm(BlogPostType::class, $blogpost);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($blogpost);
                $em->flush();
                if (!$slug) {
                    $user = $this->getUser();
                    $blogpost->setUser($user);
                    $this->addFlash('success', 'The blog post has been successfully created');
                } else {
                    $this->addFlash('success', 'The blog post has been successfully updated');
                }
                return $this->redirectToRoute("dashboard_index");
            }

            $this->addFlash('error', 'The form contains invalid data');
        }
        return $this->render('Dashboard/Administrator/BlogPost/add-edit.html.twig', array(
            "blogpost" => $blogpost,
            "form" => $form->createView(),
        ));
    }
}
