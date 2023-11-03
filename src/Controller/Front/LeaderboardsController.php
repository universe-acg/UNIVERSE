<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\LeaderboardsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeaderboardsController extends AbstractController
{
    private $leaderboardsService;

    public function __construct(LeaderboardsService $leaderboardsService)
    {
        $this->leaderboardsService = $leaderboardsService;
    }

    /**
     * @Route("/leaderboards", name="index")
     */
    public function index(): Response
    {
        $attendees = $this->leaderboardsService->getAllAttendees();
        $creators = $this->leaderboardsService->getAllCreators();

        $sortFunction = static function ($a, $b) {
            // Assuming both $a and $b have a 'points' property
            return $b->getPoints() - $a->getPoints();
        };

        usort($attendees, $sortFunction);
        usort($creators, $sortFunction);

        return $this->render('Front/Leaderboards/index.html.twig', [
            'attendees' => $attendees,
            'creators' => $creators,
        ]);
    }
}