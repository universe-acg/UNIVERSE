<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\ReelLikeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReelsLikesController extends AbstractController
{
    private $reelLikeService;

    public function __construct(ReelLikeService $reelLikeService)
    {
        $this->reelLikeService = $reelLikeService;
    }

    public function getLikesOfUser(int $userId): JsonResponse
    {
        $likes = $this->reelLikeService->getLikesOfUser($userId);

        return $this->json($likes);
    }

    public function hasUserLikedReel(int $userId, int $reelId): JsonResponse
    {
        $hasLiked = $this->reelLikeService->hasUserLikedReel($this->getUser(), $reelId);

        return $this->json(['hasLiked' => $hasLiked]);
    }

    public function getLikeOfUserForReel(int $userId, int $reelId): JsonResponse
    {
        $like = $this->reelLikeService->getLikeOfUserForReel($userId, $reelId);

        return $this->json($like);
    }

    public function getLikeCountForReel(int $reelId): JsonResponse
    {
        $likeCount = $this->reelLikeService->getLikeCountForReel($reelId);

        return $this->json(['likeCount' => $likeCount]);
    }

    public function getUsersWhoLikedReel(int $reelId): JsonResponse
    {
        $users = $this->reelLikeService->getUsersWhoLikedReel($reelId);

        return $this->json($users);
    }
}
