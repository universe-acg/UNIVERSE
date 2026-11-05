<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\ReelLike;
use App\Repository\ReelLikeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DemoMailer as Swift_Mailer;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class ReelLikeService
{
    private $em;
    private $authChecker;
    private $requestStack;
    private $kernel;
    private $cache;
    private $router;
    private $security;
    private $mailer;
    private $params;
    private $templating;
    private $urlMatcherInterface;
    private $userRepository;
    private $reelLikeRepository;

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache,
                                UrlGeneratorInterface  $router, Security $security, Swift_Mailer $mailer,
                                ParameterBagInterface  $params, \Twig\Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, ReelLikeRepository $reelLikeRepository)
    {
        $this->userRepository = $userRepository;
        $this->em = $entityManager;
        $this->authChecker = $authChecker;
        $this->requestStack = $requestStack;
        $this->kernel = $kernel;
        $this->cache = $cache;
        $this->router = $router;
        $this->security = $security;
        $this->mailer = $mailer;
        $this->params = $params;
        $this->templating = $templating;
        $this->urlMatcherInterface = $urlMatcherInterface;
        $this->reelLikeRepository = $reelLikeRepository;
    }

    /**
     * Get likes of a user
     *
     * @param int $userId
     * @return array
     */
    public function getLikesOfUser(int $userId): array
    {
        return $this->reelLikeRepository->getLikesOfUser($userId);
    }

    /**
     * Check if a user has liked a specific reel
     *
     * @param UserInterface $user
     * @param int $reelId
     * @return bool
     */
    public function hasUserLikedReel(UserInterface $user, int $reelId): bool
    {
        $userId = $user->getId();
        $like = $this->getLikeOfUserForReel($userId, $reelId);

        return $like !== null;
    }

    /**
     * Get like of a user for a specific reel
     *
     * @param int $userId
     * @param int $reelId
     * @return null|ReelLike
     */
    public function getLikeOfUserForReel(int $userId, int $reelId): ?ReelLike
    {
        return $this->reelLikeRepository->getLikeOfUserForReel($userId, $reelId);
    }

    /**
     * Get the total number of likes for a specific reel
     *
     * @param int $reelId
     * @return int
     */
    public function getLikeCountForReel(int $reelId): int
    {
        return $this->reelLikeRepository->getLikeCountForReel($reelId);
    }

    /**
     * Get the users who liked a specific reel
     *
     * @param int $reelId
     * @return array
     */
    public function getUsersWhoLikedReel(int $reelId): array
    {
        return $this->reelLikeRepository->getUsersWhoLikedReel($reelId);
    }

}
