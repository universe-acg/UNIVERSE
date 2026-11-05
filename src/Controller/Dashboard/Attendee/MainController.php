<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Controller\Dashboard\Attendee;

    use App\Controller\BaseController as Controller;
    use Symfony\Component\Routing\Attribute\Route;

    class MainController extends Controller
    {

        /**
         *
         */
#[Route(name: "index")]
        public function index()
        {
            return $this->redirectToRoute("dashboard_attendee_orders");
        }
    }
