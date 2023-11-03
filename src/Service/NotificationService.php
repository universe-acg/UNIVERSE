<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Swift_Mailer;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Twig_Environment;

class NotificationService
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
    private $notificationRepository;
    private $doctrine;

    public function __construct(ManagerRegistry        $doctrine,
                                EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache,
                                UrlGeneratorInterface  $router, Security $security, Swift_Mailer $mailer,
                                ParameterBagInterface  $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, NotificationRepository $notificationRepository)
    {
        $this->userRepository = $userRepository;
        $this->em = $entityManager;
        $this->doctrine = $doctrine;
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
        $this->notificationRepository = $notificationRepository;
    }

    public function markAllNotificationsAsRead($userId)
    {
        $this->notificationRepository->markAllNotificationsAsReadByUser($userId);
    }

    public function getNotificationsByUserId($userId)
    {
        return $this->notificationRepository->findNotificationsByUser($userId);
    }

    public function getNotificationById($userId, $notificationId)
    {
        return $this->notificationRepository->findNotificationsByUserAndId($userId, $notificationId);
    }

    public function deleteNotification($userId, $notificationId)
    {
        $notification = $this->notificationRepository->findNotificationsByUserAndId($userId, $notificationId);
        if ($notification) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($notification);
            $entityManager->flush();
        }
    }

    public function sendNotification($userId, $name, $description)
    {
        return $this->createNotification($userId, $name, $description);
    }

    public function createNotification($userId, $name, $description)
    {
        $notification = new Notification();
        $notification->setUserId($userId);
        $notification->setName($name);
        $notification->setDescription($description);
        $notification->setRead(false);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($notification);
        $entityManager->flush();

        //

        return $notification;
    }

    public function markNotificationAsRead($userId, $notificationId)
    {
        $this->notificationRepository->markNotificationAsReadByUser($userId, $notificationId);
    }

    public function getUnreadNotifications($userId)
    {
        return $this->notificationRepository->findUnreadNotificationsByUser($userId);
    }
}