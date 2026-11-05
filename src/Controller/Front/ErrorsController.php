<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ErrorsController extends AbstractController
{
    /**
     *
     */
#[Route("/access-denied", name: "access_denied")]
    public function accessDenied(): Response
    {
        return $this->render('Front/Page/access-denied.html.twig');
    }


    /**
     *
     * //    bad request error
     */
#[Route("/error400", name: "error400")]
    public function error400(): Response
    {
        return $this->render('bundles\TwigBundle\Exception\error.html.twig');
    }

    /**
     *
     */
#[Route("/error403", name: "error403")]
    public function error403(): Response
    {
        return $this->render('bundles\TwigBundle\Exception\error403.html.twig');
    }

    /**
     *
     */
#[Route("/error404", name: "error404")]
    public function error404(): Response
    {
        return $this->render('bundles\TwigBundle\Exception\error404.html.twig');
    }

    /**
     *
     */
#[Route("/error405", name: "error405")]
    public function error405(): Response
    {
        return $this->render('bundles\TwigBundle\Exception\error405.html.twig');
    }

    /**
     *
     */
#[Route("/error500", name: "error500")]
    public function error500(): Response
    {
        return $this->render('bundles\TwigBundle\Exception\error500.html.twig');
    }
}
