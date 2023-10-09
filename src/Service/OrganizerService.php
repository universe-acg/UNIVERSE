<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Service;
    
    use App\Entity\Organizer;
    use App\Repository\OrganizerRepository;
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
    
    class OrganizerService
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
        
        private $organizerRepository;
        
        private $managementClubsSocietiesService;
        
        public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, RequestStack $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface, OrganizerRepository $organizerRepository, ManagementClubsSocietiesService $managementClubsSocietiesService)
        {
            $this->managementClubsSocietiesService = $managementClubsSocietiesService;
            $this->organizerRepository = $organizerRepository;
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
        
        public function hasMember($userid): bool
        {
            return $this->organizerRepository->getById($id);
        }
        
        public function getOrganizerById($id): ?Organizer
        {
            return $this->organizerRepository->getById($id);
        }
    }