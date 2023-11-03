<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Administrator;

use App\Entity\BlogPost;
use App\Form\BlogPostType;
use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogPostController extends Controller
{

    /**
     * @Route("/manage-blog-posts", name="blog_post", methods="GET")
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services)
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');

        $blogposts = $paginator->paginate($services->getBlogPosts(array('keyword' => $keyword, 'hidden' => 'all')), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Administrator/BlogPost/index.html.twig', [
            'blogposts' => $blogposts,
        ]);
    }

    /**
     * @Route("/manage-blog-posts/add", name="blog_post_add", methods="GET|POST")
     * @Route("/manage-blog-posts/{slug}/edit", name="dashboard_organizer_blog_post_edit", methods="GET|POST")
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
                return $this->redirectToRoute("dashboard_administrator_blog_post");
            }

            $this->addFlash('error', 'The form contains invalid data');
        }
        return $this->render('Dashboard/Administrator/BlogPost/add-edit.html.twig', array(
            "blogpost" => $blogpost,
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/manage-blog-posts-admin/{slug}/edit", name="dashboard_administrator_blog_post_edit", methods="GET|POST")
     */
    public function addeditadmin(Request $request, AppServices $services, $slug = null)
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
                return $this->redirectToRoute("dashboard_administrator_blog_post");
            }

            $this->addFlash('error', 'The form contains invalid data');
        }
        return $this->render('Dashboard/Administrator/BlogPost/add-edit.html.twig', array(
            "blogpost" => $blogpost,
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/manage-blog-posts/{slug}/disable", name="blog_post_disable", methods="GET")
     * @Route("/manage-blog-posts/{slug}/delete", name="blog_post_delete", methods="GET")
     */
    public function delete(AppServices $services, $slug)
    {

        $blogpost = $services->getBlogPosts(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$blogpost) {
            $this->addFlash('error', 'The blog post can not be found');
            return $services->redirectToReferer('blog_post');
        }
        if ($blogpost->getDeletedAt() !== null) {
            $this->addFlash('error', 'The blog post has been deleted');
        } else {
            $this->addFlash('notice', 'The blog post has been deleted');
        }
        $em = $this->getDoctrine()->getManager();
        $blogpost->setHidden(true);
        $em->persist($blogpost);
        $em->flush();
        $em->remove($blogpost);
        $em->flush();
        return $services->redirectToReferer('blog_post');
    }

    /**
     * @Route("/manage-blog-posts/{slug}/restore", name="blog_post_restore", methods="GET")
     */
    public function restore($slug, AppServices $services)
    {

        $blogpost = $services->getBlogPosts(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$blogpost) {
            $this->addFlash('error', 'The blog post can not be found');
            return $services->redirectToReferer('blog_post');
        }
        $blogpost->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogpost);
        $em->flush();
        $this->addFlash('success', 'The blog post has been succesfully restored');

        return $services->redirectToReferer('blog_post');
    }

    /**
     * @Route("/manage-blog-posts/{slug}/show", name="blog_post_show", methods="GET")
     * @Route("/manage-blog-posts/{slug}/hide", name="blog_post_hide", methods="GET")
     */
    public function showhide(AppServices $services, $slug)
    {

        $blogpost = $services->getBlogPosts(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$blogpost) {
            $this->addFlash('error', 'The blog post can not be found');
            return $services->redirectToReferer('blog_post');
        }
        if ($blogpost->getHidden() === true) {
            $blogpost->setHidden(false);
            $this->addFlash('success', 'The blog post is visible');
        } else {
            $blogpost->setHidden(true);
            $this->addFlash('error', 'The blog post is hidden');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogpost);
        $em->flush();
        return $services->redirectToReferer('blog_post');
    }

}
