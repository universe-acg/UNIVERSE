<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Front;
    
    use App\Service\AppServices;
    use Knp\Component\Pager\PaginatorInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class BlogController extends AbstractController
    {
        
        /**
         * @Route("/blog", name="blog")
         */
        public function blog(Request $request, PaginatorInterface $paginator, AppServices $services): Response
        {
            $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
            $tag = ($request->query->get('tag')) == "" ? "all" : $request->query->get('tag');
            $category = ($request->query->get('category')) == "" ? "all" : $request->query->get('category');
            $blogposts = $paginator->paginate($services->getBlogPosts([
                "category" => $category,
                "keyword" => $keyword,
                "selecttags" => $tag,  // Pass the tags filter
            ])->getQuery(), $request->query->getInt('page', 1), $services->getSetting("blog_posts_per_page"), array('wrap-queries' => true));
            
            $blogpostcategories = $services->getBlogPostCategories([
                "hidden" => false,
                "keyword" => "all",
                "slug" => "all",
                "limit" => "all",
            ])->getQuery()->getResult();
            
            $totalblogposts = $services->getBlogPosts(array())->getQuery()->getResult();
            
            $tags = [];
            
            foreach ($totalblogposts as $totalblogpost) {
                
                // Assuming $tagsString contains the comma-separated values
                $tagsString = $totalblogpost->getTags();

// Split the string into an array using commas as the delimiter
                $tagsArray = explode(',', $tagsString);

// Trim each element in the array to remove leading/trailing spaces
                $tagsArray = array_map('trim', $tagsArray);
                
                foreach ($tagsArray as $tag) {
                    if (trim($tag) != "") {
                        $tags[] = $tag;
                    }
                }
            }
            
            return $this->render('Front/Blog/blog.html.twig', [
                'blogposts' => $blogposts,
                'blogpostcategories' => $blogpostcategories,
                'tags' => $tags
            ]);
        }
        
        /**
         * @Route("/blog-article/{slug}", name="blog_article")
         */
        public function blogArticle($slug, AppServices $services): Response
        {
            $blogpost = $services->getBlogPosts(array("slug" => $slug))->getQuery()->getOneOrNullResult();
            if (!$blogpost) {
                $this->addFlash('error', 'The blog post not be found');
                return $this->redirectToRoute('blog');
            }
            $blogpost->viewed();
            $em = $this->getDoctrine()->getManager();
            $em->persist($blogpost);
            $em->flush();
            
            return $this->render('Front/Blog/blog-article.html.twig', ["blogpost" => $blogpost]);
        }
        
    }
