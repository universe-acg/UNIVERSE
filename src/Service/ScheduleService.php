<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\ScheduleEntity;
use App\Repository\ScheduleRepository;
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
use Twig\Environment;

class ScheduleService
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

    private $scheduleRepository;

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache,
                                UrlGeneratorInterface  $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params,
                                \Twig\Environment       $templating, UrlMatcherInterface $urlMatcherInterface, UserRepository $userRepository,
                                ScheduleRepository     $scheduleRepository)
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
        $this->scheduleRepository = $scheduleRepository;
    }

    // ScheduleService.php
    public function createSchedule($facilityId, $dayOfWeek, $startTime, $endTime)
    {
        $schedule = new ScheduleEntity();
        $schedule->setFacilityId($facilityId);
        $schedule->setDayOfWeek($dayOfWeek);
        $schedule->setStartTime($startTime);
        $schedule->setEndTime($endTime);

        $entityManager = $this->em;
        $entityManager->persist($schedule);
        $entityManager->flush();

        return $schedule;
    }

    // ScheduleService.php
    public function updateSchedule($scheduleId, $facilityId, $dayOfWeek, $startTime, $endTime)
    {
        $schedule = $this->getScheduleById($scheduleId);

        if ($schedule) {
            $schedule->setFacilityId($facilityId);
            $schedule->setDayOfWeek($dayOfWeek);
            $schedule->setStartTime($startTime);
            $schedule->setEndTime($endTime);

            $entityManager = $this->em;
            $entityManager->flush();
        }
    }

// ScheduleService.php

    public function getScheduleById($scheduleId)
    {
        return $this->scheduleRepository->find($scheduleId);
    }

// ScheduleService.php
    public function deleteSchedule($scheduleId)
    {
        $schedule = $this->getScheduleById($scheduleId);

        if ($schedule) {
            $entityManager = $this->em;
            $entityManager->remove($schedule);
            $entityManager->flush();
        }
    }

}
