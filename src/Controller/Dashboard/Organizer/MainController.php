<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

use App\Service\AppServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{

    /**
     * @Route(name="index")
     */
    public function index(AppServices $services): Response
    {
        return $this->render('Dashboard/Organizer/index.html.twig');
    }
}
