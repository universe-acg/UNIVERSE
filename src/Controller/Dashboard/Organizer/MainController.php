<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

use App\Service\AppServices;
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends Controller
{

    /**
     *
     */
#[Route(name: "index")]
    public function index(AppServices $services): Response
    {
        return $this->render('Dashboard/Organizer/index.html.twig');
    }
}
