<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\Venue;
use App\Entity\VenueImage;
use App\Repository\UserRepository;
use App\Repository\VenueRepository;
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

class VenueService
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

    private $venueRepository;

// AthleticBookingService.php

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, \Twig\Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, VenueRepository $venueRepository)
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
        $this->venueRepository = $venueRepository;
    }

    // Returns the venues after applying the specified search criterias
    public function getVenues($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(Venue::class)->getVenues($hidden, $keyword, $slug, $limit, $count);
    }

    // Remove secondary images for a specific venue ID
    public function removeSecondaryImagesByVenueId($venueId)
    {
        // Check if $venueId is null
        if ($venueId === null) {
            // Handle the case where $venueId is null, for example, by logging an error or returning early
            return;
        }

        $secondaryImages = $this->em->getRepository(VenueImage::class)->findBy(['venue' => $venueId]);
        if ($secondaryImages) {
            foreach ($secondaryImages as $image) {
                $this->em->remove($image);
            }
        }

        $this->em->flush();
    }

    // Returns the venues after applying the specified search criterias
    public function getVenuessql($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(Venue::class)->getVenuessql($hidden, $keyword, $slug, $limit, $count);
    }

    public function getVenueBySlug($slug)
    {
        return $this->em->getRepository(Venue::class)->findOneBy(['slug' => $slug]);
    }
}
