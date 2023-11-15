<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\LiveService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LiveController extends AbstractController
{
    private $liveService;

    public function __construct(LiveService $liveService)
    {
        $this->liveService = $liveService;
    }

    /**
     * @Route("/lives", name="lives")
     */
    public function lives(Request $request, PaginatorInterface $paginator, LiveService $liveService): Response
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
        $lives = $paginator->paginate($liveService->getLives(array('keyword' => $keyword)), $request->query->getInt('page', 1), 28);
        return $this->render('Front/Live/lives.html.twig', [
            'lives' => $lives
        ]);
    }

    /**
     * @Route("/live/{slug}", name="live")
     */
    public function liveBySlug($slug): Response
    {
//        $live = $this->liveService->getLiveBySlug($slug);

//        if (!$live) {
//            throw $this->createNotFoundException('Live not found');
//        }

//        $live->viewed();
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($live);
//        $em->flush();

        // check if the user has already booked a ticket
//        $user = $this->getUser();
        // Check if the live exists

//        return $this->render('Dashboard/Front/Live/live_details.html.twig', ["live" => $live]);
        return $this->render('Front/Live/live_details.html.twig');
    }
}
