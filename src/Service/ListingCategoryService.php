<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\ListingCategory;
use App\Repository\ListingCategoryRepository;
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

class ListingCategoryService
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
    private $listingCategoryRepository;

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache,
                                UrlGeneratorInterface  $router, Security $security, Swift_Mailer $mailer,
                                ParameterBagInterface  $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, ListingCategoryRepository $listingCategoryRepository)
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
        $this->listingCategoryRepository = $listingCategoryRepository;
    }

    public function getAvailableCategories()
    {
        return $this->listingCategoryRepository->getAvailableCategories();
    }

    public function getListingBySlug($slug)
    {
        return $this->em->getRepository(ListingCategory::class)->findOneBy(['slug' => $slug]);
    }

    public function getAllCategories($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";

        return $this->listingCategoryRepository->getCategories($hidden, $keyword, $slug, $limit);
    }
}