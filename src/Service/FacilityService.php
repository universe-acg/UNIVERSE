<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\FacilityEntity;
use App\Entity\User;
use App\Repository\FacilityRepository;
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

class FacilityService
{// FacilityService.php
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
    private $facilityRepository;

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache,
                                UrlGeneratorInterface  $router, Security $security, Swift_Mailer $mailer,
                                ParameterBagInterface  $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, FacilityRepository $facilityRepository)
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
        $this->facilityRepository = $facilityRepository;
    }

// FacilityService.php
    public function deleteFacility($facilityId)
    {
        $facility = $this->getFacilityById($facilityId);

        if ($facility) {
            $entityManager = $this->em;
            $entityManager->remove($facility);
            $entityManager->flush();
        }
    }

// FacilityService.php

    public function getFacilityById($facilityId)
    {
        return $this->facilityRepository->find($facilityId);
    }

// FacilityService.php

    public function updateFacility($facilityId, $name, $description, $minPlayers, $maxPlayers, $reference, $views, $externallink)
    {
        $facility = $this->getFacilityById($facilityId);

        if ($facility) {
            $facility->setName($name);
            $facility->setDescription($description);
            $facility->setMinPlayers($minPlayers);
            $facility->setMaxPlayers($maxPlayers);
            $facility->setReference($reference);
            $facility->setViews($views);
            $facility->setExternallink($externallink);

            $entityManager = $this->em;
            $entityManager->flush();
        }
    }

    public function createFacility($name, $description, $minPlayers, $maxPlayers, $reference, $views, $externallink)
    {
        $facility = new FacilityEntity();
        $facility->setName($name);
        $facility->setDescription($description);
        $facility->setMinPlayers($minPlayers);
        $facility->setMaxPlayers($maxPlayers);
        $facility->setReference($reference);
        $facility->setViews($views);
        $facility->setExternallink($externallink);

        $entityManager = $this->em;
        $entityManager->persist($facility);
        $entityManager->flush();

        return $facility;
    }

    public function getUserById($userId): ?User
    {
        return $this->userRepository->getById($userId);
    }
}