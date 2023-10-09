<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Dashboard\Administrator;
    
    use App\Entity\HelpCenterArticle;
    use App\Form\HelpCenterArticleType;
    use App\Service\AppServices;
    use Knp\Component\Pager\PaginatorInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    
    class HelpCenterArticleController extends Controller
    {
        
        /**
         * @Route("/manage-help-center/articles", name="help_center_article", methods="GET")
         */
        public function index(Request $request, PaginatorInterface $paginator, AppServices $services)
        {
            $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
            
            $articles = $services->getHelpCenterArticles(array('keyword' => $keyword, 'hidden' => 'all', 'sort' => 'DESC', 'order' => 'updatedAt'))->getQuery()->getResult();
            
            // Custom sorting function
            usort($articles, static function (HelpCenterArticle $a, HelpCenterArticle $b) {
                $categorya = $a->getCategory()->getSlug();
                $categoryb = $b->getCategory()->getSlug();
                
                // Define the order of categories
                $categoryOrder = ['account', 'attendee', 'creator'];
                
                // Compare the category order
                $categoryComparison = array_search($categorya, $categoryOrder, true) - array_search($categoryb, $categoryOrder, true);
                
                // If categories are the same, sort by the first one or two letters (if numeric)
                if ($categoryComparison === 0) {
                    $aName = $a->getTitle();
                    $bName = $b->getTitle();
                    
                    // Extract the first one or two letters (if numeric)
                    $aFirstLetters = preg_replace('/[^0-9]*/', '', substr($aName, 0, 2));
                    $bFirstLetters = preg_replace('/[^0-9]*/', '', substr($bName, 0, 2));
                    
                    // Check if both first letters are non-numeric
                    if (!is_numeric($aFirstLetters) && !is_numeric($bFirstLetters)) {
                        // Skip the sort for this iteration by returning 0
                        return 0;
                    }
                    
                    // Check if both first letters are non-numeric
                    if (!is_numeric($aFirstLetters) || !is_numeric($bFirstLetters)) {
                        // Skip the sort for this iteration by returning 0
                        return 0;
                    }
                    
                    // Compare the first letters (numeric values only)
                    $alphaComparison = (int)$aFirstLetters - (int)$bFirstLetters;
                    
                    
                    // If both are non-numeric or equal numeric values, sort alphabetically
                    if ($alphaComparison === 0) {
                        return strcmp($aName, $bName);
                    }
                    
                    return $alphaComparison;
                }
                
                return $categoryComparison;
            });
            
            $articles = $paginator->paginate($articles, $request->query->getInt('page', 1), 10, array('wrap-queries' => true));
            
            return $this->render('Dashboard/Administrator/HelpCenter/Article/index.html.twig', [
                'articles' => $articles,
            ]);
        }
        
        /**
         * @Route("/manage-help-center/articles/add", name="help_center_article_add", methods="GET|POST")
         * @Route("/manage-help-center/articles/{slug}/edit", name="help_center_article_edit", methods="GET|POST")
         */
        public function addedit(Request $request, AppServices $services, $slug = null)
        {
            $em = $this->getDoctrine()->getManager();
            
            if (!$slug) {
                $article = new HelpCenterArticle();
            } else {
                $article = $services->getHelpCenterArticles(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
                if (!$article) {
                    $this->addFlash('error', 'The article can not be found');
                    return $services->redirectToReferer('help_center_article');
                }
            }
            
            $form = $this->createForm(HelpCenterArticleType::class, $article);
            $form->handleRequest($request);
            
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $em->persist($article);
                    $em->flush();
                    if (!$slug) {
                        $this->addFlash('success', 'The article has been successfully created');
                    } else {
                        $this->addFlash('success', 'The article has been successfully updated');
                    }
                    return $this->redirectToRoute("dashboard_administrator_help_center_article");
                } else {
                    $this->addFlash('error', 'The form contains invalid data');
                }
            }
            return $this->render('Dashboard/Administrator/HelpCenter/Article/add-edit.html.twig', array(
                "article" => $article,
                "form" => $form->createView(),
            ));
        }
        
        /**
         * @Route("/manage-help-center/articles/{slug}/featured", name="help_center_article_featured", methods="GET")
         * @Route("/manage-help-center/articles/{slug}/notfeatured", name="help_center_article_notfeatured", methods="GET")
         */
        public function featured(AppServices $services, $slug)
        {
            
            $article = $services->getHelpCenterArticles(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
            if (!$article) {
                $this->addFlash('error', 'The article can not be found');
                return $services->redirectToReferer('help_center_article');
            }
            if ($article->getFeatured() === true) {
                $article->setFeatured(false);
                $this->addFlash('error', 'The article is not featured anymore');
            } else {
                $article->setFeatured(true);
                $this->addFlash('success', 'The article is featured');
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('dashboard_administrator_help_center_article');
        }
        
        /**
         * @Route("/manage-help-center/articles/{slug}/disable", name="help_center_article_disable", methods="GET")
         * @Route("/manage-help-center/articles/{slug}/delete", name="help_center_article_delete", methods="GET")
         */
        public function delete(AppServices $services, $slug)
        {
            
            $article = $services->getHelpCenterArticles(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
            if (!$article) {
                $this->addFlash('error', 'The article can not be found');
                return $services->redirectToReferer('help_center_article');
            }
            if ($article->getDeletedAt() !== null) {
                $this->addFlash('error', 'The article has been deleted');
            } else {
                $this->addFlash('notice', 'The article has been disabled');
            }
            $em = $this->getDoctrine()->getManager();
            $article->setHidden(true);
            $em->persist($article);
            $em->flush();
            $em->remove($article);
            $em->flush();
            return $services->redirectToReferer('help_center_article');
        }
        
        /**
         * @Route("/manage-help-center/articles/{slug}/restore", name="help_center_article_restore", methods="GET")
         */
        public function restore($slug, AppServices $services)
        {
            
            $article = $services->getHelpCenterArticles(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
            if (!$article) {
                $this->addFlash('error', 'The article can not be found');
                return $services->redirectToReferer('help_center_article');
            }
            $article->setDeletedAt(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'The article has been succesfully restored');
            
            return $services->redirectToReferer('help_center_article');
        }
        
        /**
         * @Route("/manage-help-center/articles/{slug}/show", name="help_center_article_show", methods="GET")
         * @Route("/manage-help-center/articles/{slug}/hide", name="help_center_article_hide", methods="GET")
         */
        public function showhide(AppServices $services, $slug)
        {
            
            $article = $services->getHelpCenterArticles(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
            if (!$article) {
                $this->addFlash('error', 'The article can not be found');
                return $services->redirectToReferer('help_center_article');
            }
            if ($article->getHidden() === true) {
                $article->setHidden(false);
                $this->addFlash('success', 'The article is visible');
            } else {
                $article->setHidden(true);
                $this->addFlash('error', 'The article is hidden');
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $services->redirectToReferer('help_center_article');
        }
        
    }
