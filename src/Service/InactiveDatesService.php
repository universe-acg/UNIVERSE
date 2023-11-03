<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\InactiveDatesRepository;
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

class InactiveDatesService
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

    private $inactiveDatesRepository;

// InactiveDatesService.php
// InactiveDatesService.php

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache,
                                UrlGeneratorInterface  $router, Security $security, Swift_Mailer $mailer,
                                ParameterBagInterface  $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, InactiveDatesRepository $inactiveDatesRepository)
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
        $this->inactiveDatesRepository = $inactiveDatesRepository;
    }

// InactiveDatesService.php
    public function deleteInactiveDate($inactiveDateId)
    {
        $inactiveDate = $this->getInactiveDateById($inactiveDateId);

        if ($inactiveDate) {
            $entityManager = $this->em;
            $entityManager->remove($inactiveDate);
            $entityManager->flush();
        }
    }

// InactiveDatesService.php

    public function getInactiveDateById($inactiveDateId)
    {
        return $this->inactiveDatesRepository->find($inactiveDateId);
    }

    public function updateInactiveDate($inactiveDateId, $facilityId, $date)
    {
        $inactiveDate = $this->getInactiveDateById($inactiveDateId);

        if ($inactiveDate) {
            $inactiveDate->setFacilityId($facilityId);
            $inactiveDate->setDate($date);

            $entityManager = $this->em;
            $entityManager->flush();
        }
    }

    public function createInactiveDate($facilityId, $date)
    {
        $inactiveDate = new InactiveDate();
        $inactiveDate->setFacilityId($facilityId);
        $inactiveDate->setDate($date);

        $entityManager = $this->em;
        $entityManager->persist($inactiveDate);
        $entityManager->flush();

        return $inactiveDate;
    }

    public function getUserById($userId): ?User
    {
        return $this->userRepository->getById($userId);
    }
}