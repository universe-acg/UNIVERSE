<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\Listing;
use App\Repository\ListingRepository;
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

class ListingService
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
    private $listingRepository;

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache,
                                UrlGeneratorInterface  $router, Security $security, Swift_Mailer $mailer,
                                ParameterBagInterface  $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, ListingRepository $listingRepository)
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
        $this->listingRepository = $listingRepository;
    }

    // Returns the venues after applying the specified search criterias
    public function getListings($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(Listing::class)->getListings($hidden, $keyword, $slug, $limit, $count);
    }

    public function getListingsForEvent($eventId, $limit = null)
    {
        return $this->em->getRepository(Listing::class)->getListingsForEvent($eventId, $limit);
    }

    public function getMyListings($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
        $organizerid = array_key_exists('organizerid', $criterias) ? $criterias['organizerid'] : false;

        return $this->em->getRepository(Listing::class)->getMyListings($hidden, $keyword, $slug, $limit, $count, $organizerid);
    }

    // Returns the venues after applying the specified search criterias
    public function getListingssql($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(Listing::class)->getListingssql($hidden, $keyword, $slug, $limit, $count);
    }

    public function getListingBySlug($slug)
    {
        return $this->em->getRepository(Listing::class)->findOneBy(['slug' => $slug]);
    }
}