<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\FacilityEntity;
use App\Entity\FacilityEntityImage;
use App\Repository\FacilityRepository;
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

class AthleticService
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

    private $facilityService;

    private $venueRepository;

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, \Twig\Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                UserRepository         $userRepository, FacilityRepository $venueRepository, FacilityService $facilityService)
    {
        $this->facilityService = $facilityService;
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
    public function getAthletics($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(FacilityEntity::class)->getFacilityEntitys($hidden, $keyword, $slug, $limit, $count);
    }

    // Remove secondary images for a specific venue ID
    public function removeSecondaryImagesByAthleticFacilityId($id)
    {
        $facility = $this->facilityService->findById($id);

        $secondaryImages = [];

        if ($secondaryImages) {
            $secondaryImages = $facility->getSecondaryImages();
        }

        foreach ($secondaryImages as $image) {
            $this->em->remove($image);
        }

        $this->em->flush();
    }

    // Remove secondary images for a specific venue ID
    public function removeSecondaryImagesByAthleticsId($venueId)
    {
        $secondaryImages = $this->em->getRepository(FacilityEntityImage::class)->findBy(['venue' => $venueId]);

        foreach ($secondaryImages as $image) {
            $this->em->remove($image);
        }

        $this->em->flush();
    }

    // Returns the venues after applying the specified search criterias
    public function getFacilityEntityssql($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(FacilityEntity::class)->getFacilityEntityssql($hidden, $keyword, $slug, $limit, $count);
    }


    public function getAthleticBySlug($slug)
    {
        return $this->em->getRepository(FacilityEntity::class)->findOneBy(['slug' => $slug]);
    }
}
