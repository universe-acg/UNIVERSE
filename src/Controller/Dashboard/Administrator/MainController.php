<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Controller\Dashboard\Administrator;

    use App\Controller\BaseController as Controller;
    use Symfony\Component\Routing\Attribute\Route;

    class MainController extends Controller
    {

        /**
         *
         */
#[Route("/", name: "index", methods: ["GET"])]
        public function index()
        {
            return $this->render('Dashboard/Administrator/index.html.twig');
        }

    }
