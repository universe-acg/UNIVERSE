<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpCenterController extends AbstractController
{

    /**
     * @Route("/help-center", name="help_center")
     */
    public function helpCenter(Request $request): Response
    {
        return $this->render('Front/HelpCenter/index.html.twig');
    }

    /**
     * @Route("/help-center/{slug}", name="help_center_category")
     */
    public function helpCenterCategory($slug, AppServices $services): Response
    {
        $category = $services->getHelpCenterCategories(array("slug" => $slug))->getQuery()->getOneOrNullResult();
        if (!$category) {
            $this->addFlash('error', 'The category not be found');
            return $this->redirectToRoute('help_center');
        }
        return $this->render('Front/HelpCenter/category.html.twig', ["category" => $category]);
    }

    /**
     * @Route("/help-center/article/{slug}", name="help_center_article")
     */
    public function helpCenterArticle($slug, AppServices $services): Response
    {
        $article = $services->getHelpCenterArticles(array("slug" => $slug))->getQuery()->getOneOrNullResult();
        if (!$article) {
            $this->addFlash('error', 'The article not be found');
            return $this->redirectToRoute('help_center');
        }
        $article->viewed();
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->render('Front/HelpCenter/article.html.twig', ["article" => $article]);
    }

}
