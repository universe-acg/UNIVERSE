<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Service;
    
    use App\Entity\ManagementClubsSocieties;
    use App\Repository\ManagementClubsSocietiesRepository;
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
    
    class ManagementClubsSocietiesService
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
        
        public function __construct(ManagementClubsSocietiesRepository $repository, EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, RequestStack $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface, OrganizerRepository $organizerRepository)
        {
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
        
        public function findAllUniverseRequest()
        {
            return $this->repository->findAllUniverseRequest();
        }
        
        public function findAllAssociationRequestsForUser($userId)
        {
            return $this->repository->findAllAssociationRequestsForUser($userId);
        }
        
        public function ismemberof($attendeeid, $orgid)
        {
            $row = $this->repository->findRowByUserIdAndOrgId($attendeeid, $orgid);
            return $row && $row->getStatus() == 1;
        }
        
        public function findRowByUserIdAndOrgId($userId, $orgId): ?ManagementClubsSocieties
        {
            return $this->repository->findRowByUserIdAndOrgId($userId, $orgId);
        }
        
        public function isaccepted($attendeeid, $orgid)
        {
            $row = $this->repository->findRowByUserIdAndOrgId($attendeeid, $orgid);
            return $row && $row->getStatus() == 1;
        }
        
        public function isinitiatedbyme($associationId, $userId): bool
        {
            return $this->repository->isInitiatedByUser($associationId, $userId);
        }
        
        public function isrejected($attendeeid, $orgid)
        {
            $row = $this->repository->findRowByUserIdAndOrgId($attendeeid, $orgid);
            return $row && $row->getStatus() == -1;
        }
        
        public function ispending($attendeeid, $orgid)
        {
            $row = $this->repository->findRowByUserIdAndOrgId($attendeeid, $orgid);
            return $row && $row->getStatus() == 0;
        }
        
        public function hasmemberofid($orgid, $attendeeid)
        {
            return $this->repository->doesRowExist($orgid, $attendeeid);
        }
        
        public function doesRowExist($userId, $orgId)
        {
            return $this->repository->doesRowExist($userId, $orgId);
        }
        
        public function findAllAssociationRequestsForClub($clubId)
        {
            return $this->repository->findAllAssociationRequestsForClub($clubId);
        }
        
        public function findAssociationRequestById($associationId)
        {
            return $this->repository->findAssociationRequestById($associationId);
        }
        
        public function findAssociationRequestsWithStatus($status)
        {
            return $this->repository->findAssociationRequestsWithStatus($status);
        }
        
        public function saveAssociationRequest(ManagementClubsSocieties $association)
        {
            $this->repository->saveAssociationRequest($association);
        }
        
        public function deleteAssociationRequest(ManagementClubsSocieties $association)
        {
            $this->repository->deleteAssociationRequest($association);
        }
        
        public function findAssociationRequestsInitiatedByUser($userId)
        {
            return $this->repository->findAssociationRequestsInitiatedByUser($userId);
        }
        
        public function findAssociationRequestsByClubAndStatus($clubId, $status)
        {
            return $this->repository->findAssociationRequestsByClubAndStatus($clubId, $status);
        }
        
        public function findAssociationRequestsByUserAndStatus($userId, $status)
        {
            return $this->repository->findAssociationRequestsByUserAndStatus($userId, $status);
        }
        
        public function countAssociationRequestsByStatus($status)
        {
            return $this->repository->countAssociationRequestsByStatus($status);
        }
        
        public function findAssociationRequestsByRole($role)
        {
            return $this->repository->findAssociationRequestsByRole($role);
        }
        
        public function doSomething()
        {
            return "adaasd";
        }
    }