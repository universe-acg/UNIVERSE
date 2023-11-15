<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\AthleticBooking;
use App\Repository\AthleticBookingRepository;
use App\Repository\UserRepository;
use DateInterval;
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

class AthleticBookingService
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

    private $athleticBookingRepository;

// AthleticBookingService.php

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, AthleticBookingRepository $athleticBookingRepository)
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
        $this->athleticBookingRepository = $athleticBookingRepository;
    }

// AthleticBookingService.php
    public function deleteAthleticBooking($athleticBookingId)
    {
        $athleticBooking = $this->getAthleticBookingById($athleticBookingId);

        if ($athleticBooking) {
            $entityManager = $this->em;
            $entityManager->remove($athleticBooking);
            $entityManager->flush();
        }
    }

    public function getAthleticBookingById($athleticBookingId)
    {
        return $this->athleticBookingRepository->getByFacilityId($athleticBookingId);
    }

    public function getAthleticBookingByFacility($facility)
    {
        return $this->athleticBookingRepository->find($athleticBookingId);
    }


    // join

    // dates stuff

    public function getAthleticBookings()
    {
        return $this->athleticBookingRepository->find();
    }

// AthleticBookingService.php

    public function createAthleticBooking($facilityId, $userId, $scheduleId, $bookingDate, $duration)
    {
        $schedule = $this->getScheduleById($scheduleId);

        // Calculate end time based on start time and duration
        $startTime = $schedule->getStartTime();
        $endTime = $startTime->add(new DateInterval('PT' . $duration . 'M'));

        // Check for conflicts with existing reservations
        $conflicts = $this->checkReservationConflicts($facilityId, $bookingDate, $startTime, $endTime);

        if ($conflicts) {
            // Handle conflicts, e.g., return an error or provide alternative times
            // Add appropriate error handling and response.
        } else {
            // Create the reservation
            $athleticBooking = new AthleticBooking();
            $athleticBooking->setFacilityId($facilityId);
            $athleticBooking->setUserId($userId);
            $athleticBooking->setScheduleId($scheduleId);
            $athleticBooking->setBookingDate($bookingDate);
            $athleticBooking->setDuration($duration);

            $entityManager = $this->em;
            $entityManager->persist($athleticBooking);
            $entityManager->flush();

            return $athleticBooking;
        }
    }
}