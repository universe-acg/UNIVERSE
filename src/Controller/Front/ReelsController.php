<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\ReelLikeService;
use App\Service\ReelService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReelsController extends AbstractController
{
    private $reelService;

    public function __construct(ReelService $reelService)
    {
        $this->reelService = $reelService;
    }

    /**
     * @Route("/reels", name="reels")
     */
    public function reels(Request $request, PaginatorInterface $paginator, ReelService $reelService, ReelLikeService $reelLikeService): Response
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
        $reels = $reelService->getReels(array('keyword' => $keyword));

        // Get initial results
        $initialReels = $reelService->getReels(array('keyword' => $keyword));

// Define the number of copies you want
        $numberOfCopies = 20; // Change this to your desired number

// Create an array to hold the multiplied results
        $multipledReels = array();

// Multiply the results
        for ($i = 0; $i < $numberOfCopies; $i++) {
            $multipledReels = array_merge($multipledReels, $initialReels);
        }

// Shuffle the array to randomize the order
        shuffle($multipledReels);

        $reels = $paginator->paginate($multipledReels, $request->query->getInt('page', 1), 50);

        return $this->render('Front/Reels/reels.html.twig', [
            'reels' => $reels
        ]);
    }

    /**
     * @Route("/load-more-reels", name="load_more_reels", methods={"GET"})
     */
    public function loadMoreReels(Request $request, PaginatorInterface $paginator, ReelService $reelService): JsonResponse
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
//        $reels = $reelService->getReels(array('keyword' => $keyword));

        // Get initial results
        $initialReels = $reelService->getReels(array('keyword' => $keyword));
        $page = $request->query->getInt('page', 1);
        $numberOfCopies = 20; // Change this to your desired number
        $multipledReels = array();

// Multiply the results
        for ($i = 0; $i < $numberOfCopies; $i++) {
            $multipledReels = array_merge($multipledReels, $initialReels);
        }

// Shuffle the array to randomize the order
        shuffle($multipledReels);
        // Assuming $multipledReels is still available
        $reels = $paginator->paginate($multipledReels, $page, 25);

        $html = $this->renderView('Front/Reels/partial-reel.html.twig', ['reels' => $reels]);

        return $this->json(['html' => $html, 'totalPages' => $reels->getTotalItemCount()]);
//        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
//        $page = $request->query->getInt('page', 1);
//
//        // Get initial results
//        $initialReels = $reelService->getReels(array('keyword' => $keyword));
//
//        // Paginate the original set of results directly
//        $reels = $paginator->paginate($initialReels, $page, 2);
//
//        $html = $this->renderView('Front/Reels/partial-reel.html.twig', ['reels' => $reels]);
//
//        return $this->json(['html' => $html, 'totalPages' => $reels->getTotalItemCount()]);
    }

    /**
     * @Route("/reel/{slug}", name="reel")
     */
    public function reelBySlug($slug): Response
    {
        $reel = $this->reelService->getReelBySlug($slug);

        if (!$reel) {
            throw $this->createNotFoundException('Reel not found');
        }

        $reel->viewed();
        $em = $this->getDoctrine()->getManager();
        $em->persist($reel);
        $em->flush();

        // check if the user has already booked a ticket
        $user = $this->getUser();
        // Check if the reel exists

        return $this->render('Dashboard/Front/Reel/reel_details.html.twig', ["reel" => $reel]);
    }
}
