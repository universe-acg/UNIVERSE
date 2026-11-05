<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{

    /**
     *
     */
#[Route("/blog", name: "blog")]
    public function blog(Request $request, PaginatorInterface $paginator, AppServices $services): Response
    {

        $keyword = $this->getQueryParam($request, 'keyword') != '' ? $this->getQueryParam($request, 'keyword') : 'all';
        $tag = $this->getQueryParam($request, 'tag') != '' ? $this->getQueryParam($request, 'tag') : 'all';
        $category = $this->getQueryParam($request, 'category') != '' ? $this->getQueryParam($request, 'category') : 'all';
        $sortedby = $this->getQueryParam($request, 'sortedby') != '' ? $this->getQueryParam($request, 'sortedby') : 'all';

        $blogposts = $this->getPaginatedBlogPosts($services, $keyword, $category, $tag, $sortedby, $paginator, $request);

        $blogpostcategories = $this->getBlogPostCategories($services);

        $tags = $this->getTagsFromBlogPosts($services);

        return $this->render('Front/Blog/blog.html.twig', [
            'blogposts' => $blogposts,
            'blogpostcategories' => $blogpostcategories,
            'tags' => $tags,
            'sortedby' => $sortedby
        ]);
    }

    private function getQueryParam(Request $request, $paramName)
    {
        return $request->query->get($paramName, 'all');
    }


    private function getPaginatedBlogPosts(AppServices $services, $keyword, $category, $tag, $sortedby, PaginatorInterface $paginator, Request $request)
    {
        $criteria = [
            "category" => $category,
            "keyword" => $keyword,
            "selecttags" => $tag,
            'sortedby' => $sortedby
        ];

        $query = $services->getBlogPosts($criteria)->getQuery();

        return $paginator->paginate($query, $request->query->getInt('page', 1), 16, ['wrap-queries' => true]);
    }


    private function getBlogPostCategories(AppServices $services)
    {
        $criteria = [
            "hidden" => false,
            "keyword" => "all",
            "slug" => "all",
            "limit" => "all"
        ];

        return $services->getBlogPostCategories($criteria)->getQuery()->getResult();
    }


    private function getTagsFromBlogPosts(AppServices $services)
    {
        $totalblogposts = $services->getBlogPosts([])->getQuery()->getResult();
        $tags = [];

        foreach ($totalblogposts as $totalblogpost) {
            $tagsString = $totalblogpost->getTags();
            $tagsArray = explode(',', $tagsString);
            $tagsArray = array_map('trim', $tagsArray);

            foreach ($tagsArray as $tag) {
                if (trim($tag) != "") {
                    $tags[] = $tag;
                }
            }
        }

        return $tags;
    }



















    /**
     *
     */
#[Route("/blogvideos", name: "blogvideos")]
    public function blogvideos(Request $request, PaginatorInterface $paginator, AppServices $services): Response
    {
        $blogposts = $services->getBlogPostVideos(array())->getQuery()->getResult();

        foreach ($blogposts as $blogpost) {

            $randomLikes = random_int(1, 100); // Generate a random number between 1 and 100.
            $randomComments = $this->generateRandomComments(5); // Generate an array of random comments (5 in this example).

            $blogpost->setLikes($randomLikes);
            $blogpost->setComments($randomComments);
        }

        usort($blogposts, function ($a, $b) {
            return $b->getLikes() - $a->getLikes();
        });

        return $this->render('Front/Blog/blogvideos.html.twig', [
            'blogposts' => $blogposts,
        ]);
    }

    public function generateRandomComments($count)
    {
        $comments = [];
        for ($i = 0; $i < $count; $i++) {
            $randomComment = $this->generateRandomComment();
            $comments[] = $randomComment;
        }
        return $comments;
    }

    public function generateRandomComment()
    {
        $commentsArray = [
            "This is great!",
            "Nice video!",
            "I love this post!",
            "Awesome content!",
            "Keep it up!",
            "Amazing work!",
            "You're doing great!",
        ];

        $randomIndex = array_rand($commentsArray);
        return $commentsArray[$randomIndex];
    }

    /**
     *
     */
#[Route("/blog-article/{slug}", name: "blog_article")]
    public function blogArticle($slug, AppServices $services): Response
    {

        $blogpost = $this->getBlogPostBySlug($services, $slug);

        if (!$blogpost) {

            $this->addFlash('error', 'The blog post could not be found');
            return $this->redirectToRoute('blog');
        }

        $this->markBlogPostAsViewed($blogpost);

        return $this->render('Front/Blog/blog-article.html.twig', ['blogpost' => $blogpost]);
    }

    private function getBlogPostBySlug(AppServices $services, $slug)
    {
        return $services->getBlogPosts(['slug' => $slug])->getQuery()->getOneOrNullResult();
    }

    private function markBlogPostAsViewed($blogpost)
    {
        $blogpost->viewed();
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogpost);
        $em->flush();
    }
}
