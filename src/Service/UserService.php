<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

class UserService
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

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, RequestStack $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router,
                                Security               $security, Swift_Mailer $mailer, ParameterBagInterface $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface, UserRepository $userRepository)
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
    }

    public function getUsersInChat($chat)
    {
        return $chat->getParticipants();
    }

    public function getUsers($criterias)
    {
        $role = array_key_exists('role', $criterias) ? $criterias['role'] : "all";
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $organizername = array_key_exists('organizername', $criterias) ? $criterias['organizername'] : "all";
        $organizerslug = array_key_exists('organizerslug', $criterias) ? $criterias['organizerslug'] : "all";
        $username = array_key_exists('username', $criterias) ? $criterias['username'] : "all";
        $email = array_key_exists('email', $criterias) ? $criterias['email'] : "all";
        $firstname = array_key_exists('firstname', $criterias) ? $criterias['firstname'] : "all";
        $lastname = array_key_exists('lastname', $criterias) ? $criterias['lastname'] : "all";
        $enabled = array_key_exists('enabled', $criterias) ? $criterias['enabled'] : true;
        $followedby = array_key_exists('followedby', $criterias) ? $criterias['followedby'] : "all";
        $apiKey = array_key_exists('apikey', $criterias) ? $criterias['apikey'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $hasboughtticketforEvent = array_key_exists('hasboughtticketfor', $criterias) ? $criterias['hasboughtticketfor'] : "all";
        $hasboughtticketforOrganizer = array_key_exists('hasboughtticketfororganizer', $criterias) ? $criterias['hasboughtticketfororganizer'] : "all";
        $hasbookedticketfor = array_key_exists('hasbookedticketfor', $criterias) ? $criterias['hasbookedticketfor'] : "all";
        $isOnHomepageSlider = array_key_exists('isOnHomepageSlider', $criterias) ? $criterias['isOnHomepageSlider'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository("App\Entity\User")->getUsers($role, $keyword, $organizername, $organizerslug, $username, $email, $firstname, $lastname, $enabled, $slug, $followedby, $hasboughtticketforEvent, $hasboughtticketforOrganizer, $hasbookedticketfor, $apiKey, $isOnHomepageSlider, $limit, $count);
    }


    public function getAttendees()
    {
        return $this->em->getRepository("App\Entity\User")->getUsersWithRoleAttendee();
    }


    public function getMembers()
    {
        $user = $this->getUserById($this->security->getUser()->getId());
        // TODO return only improved
        return $user->getOrganizer()->getMembers();
    }

    public function getUserById($userId): ?User
    {
        return $this->userRepository->getById($userId);
    }
}