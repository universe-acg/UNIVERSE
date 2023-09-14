<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

use App\Entity\BlogPost;
use App\Form\BlogPostType;
use App\Service\AppServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class BlogPostCategoryController extends Controller
{

    /**
     * @Route("/manage-blog-post-categories/add", name="blog_post_category_add", methods="GET|POST")
     * @Route("/manage-blog-post-categories/{slug}/edit", name="blog_post_category_edit", methods="GET|POST")
     */
    public function addedit(Request $request, AppServices $services, TranslatorInterface $translator, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $blogpost = new BlogPost();
        } else {
            $blogpost = $services->getBlogPosts(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
            if (!$blogpost) {
                $this->addFlash('error', $translator->trans('The blog post can not be found'));
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
                    $this->addFlash('success', $translator->trans('The blog post has been successfully created'));
                } else {
                    $this->addFlash('success', $translator->trans('The blog post has been successfully updated'));
                }
                return $this->redirectToRoute("dashboard_organizer_index");
            }

            $this->addFlash('error', $translator->trans('The form contains invalid data'));
        }
        return $this->render('Dashboard/Administrator/BlogPost/add-edit.html.twig', array(
            "blogpost" => $blogpost,
            "form" => $form->createView(),
        ));
    }
}
