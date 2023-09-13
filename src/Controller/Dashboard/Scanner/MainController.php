<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Scanner;

use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{

    /**
     * @Route(name="index")
     * @throws InvalidDatabaseException
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services)
    {
        $events = $paginator->paginate($services->getEvents(array("canbescannedby" => $this->getUser()->getScanner()))->getQuery(), $request->query->getInt('page', 1), 12, array('wrap-queries' => true));
        return $this->render('Dashboard/Scanner/index.html.twig', [
            "events" => $events
        ]);
    }

}
