<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Service;
    
    use App\Entity\UniverseGbMember;
    use App\Repository\ManagementClubsSocietiesRepository;
    use App\Repository\OrganizerRepository;
    use App\Repository\UniverseGbMemberRepository;
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
    
    class UniverseGbMemberService
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
        private $repository;
        
        private $universeGbMemberRepository;
        
        public function __construct(UniverseGbMemberRepository $universeGbMemberRepository, ManagementClubsSocietiesRepository $repository, EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, RequestStack $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface, OrganizerRepository $organizerRepository)
        {
            $this->universeGbMemberRepository = $universeGbMemberRepository;
            $this->repository = $repository;
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
            $this->organizerRepository = $organizerRepository;
        }
        
        public function add(UniverseGbMember $newMember)
        {
            $this->em->persist($newMember);
            $this->em->flush();
        }
        
        
        public function getById($id)
        {
            return $this->universeGbMemberRepository->findBy(['id' => $id]);
        }
        
        public function getByOrg($orgId)
        {
            return $this->universeGbMemberRepository->findBy(['org' => $orgId]);
        }
        
        public function getByAttendee($attendeeId)
        {
            return $this->universeGbMemberRepository->findBy(['attendee' => $attendeeId]);
        }
        
        public function getAll()
        {
            return $this->universeGbMemberRepository->findAll();
        }
        
        public function updateByOrg($orgId, $data)
        {
            $members = $this->universeGbMemberRepository->findBy(['org' => $orgId]);
            foreach ($members as $member) {
                // Update properties based on $data
                // Example: $member->setTitle($data['title']);
                // ... Update other properties ...
                $this->em->persist($member);
            }
            $this->em->flush();
        }
        
        public function deleteById($id)
        {
            $member = $this->universeGbMemberRepository->find($id);
            if ($member) {
                $this->em->remove($member);
                $this->em->flush();
            }
        }
        
        public function updateById($id, $data)
        {
            $member = $this->universeGbMemberRepository->find($id);
            if ($member) {
                // Update properties based on $data
                // Example: $member->setTitle($data['title']);
                // ... Update other properties ...
                $this->em->persist($member);
                $this->em->flush();
            }
        }
        
    }