<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelpCenterController extends AbstractController
{

    /**
     *
     */
#[Route("/help-center", name: "help_center")]
    public function helpCenter(): Response
    {
        return $this->render('Front/HelpCenter/index.html.twig');
    }

    /**
     *
     */
#[Route("/help-center/{slug}", name: "help_center_category")]
    public function helpCenterCategory($slug, AppServices $services): Response
    {
        $category = $services->getHelpCenterCategoriesFront(array("slug" => $slug));
        if (!isset($category[0])) {
            $this->addFlash('error', 'The category not be found');
            return $this->redirectToRoute('help_center');
        }
        return $this->render('Front/HelpCenter/category.html.twig', ["category" => $category[0]]);
    }

    /**
     *
     */
#[Route("/help-center/article/{slug}", name: "help_center_article")]
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
