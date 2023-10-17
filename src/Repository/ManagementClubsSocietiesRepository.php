<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\ManagementClubsSocieties;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    class ManagementClubsSocietiesRepository extends ServiceEntityRepository
    {
        
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, ManagementClubsSocieties::class);
        }
        
        public function findAllUniverseRequest()
        {
            return $this->createQueryBuilder('u')
                ->getQuery()
                ->getResult();
        }
        
        public function doesRowExist($userId, $orgId)
        {
            return $this->createQueryBuilder('u')
                    ->select('COUNT(u.id)')
                    ->where('u.user = :userId')
                    ->andWhere('u.org = :orgId')
                    ->setParameter('userId', $userId)
                    ->setParameter('orgId', $orgId)
                    ->orderBy('u.createdAt', 'DESC')
                    ->getQuery()
                    ->getSingleScalarResult() > 0;
        }
        
        public function findAllAssociationRequestsForUser($userId)
        {
            return $this->createQueryBuilder('u')
                ->where('u.user = :userId')
                ->setParameter('userId', $userId)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        
        public function findAllAssociationRequestsForClub($clubId)
        {
            return $this->createQueryBuilder('u')
                ->where('u.org = :clubId')
                ->setParameter('clubId', $clubId)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        
        public function findAssociationRequestById($associationId)
        {
            return $this->createQueryBuilder('u')
                ->where('u.id = :associationId')
                ->setParameter('associationId', $associationId)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        public function findAssociationRequestsWithStatus($status)
        {
            return $this->createQueryBuilder('u')
                ->where('u.status = :status')
                ->setParameter('status', $status)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        
        public function isInitiatedByUser($associationId, $userId)
        {
            return $this->createQueryBuilder('u')
                    ->select('COUNT(u.id)')
                    ->where('u.id = :associationId')
                    ->andWhere('u.initiatedBy = :userId')
                    ->setParameter('associationId', $associationId)
                    ->setParameter('userId', $userId)
                    ->orderBy('u.createdAt', 'DESC')
                    ->getQuery()
                    ->getSingleScalarResult() > 0;
        }
        
        public function saveAssociationRequest(ManagementClubsSocieties $association)
        {
            $this->getEntityManager()->persist($association);
            $this->getEntityManager()->flush();
        }
        
        public function deleteAssociationRequest(ManagementClubsSocieties $association)
        {
            $this->getEntityManager()->remove($association);
            $this->getEntityManager()->flush();
        }
        
        public function findRowByUserIdAndOrgId($userId, $orgId)
        {
            return $this->createQueryBuilder('u')
                ->where('u.user = :userId')
                ->andWhere('u.org = :orgId')
                ->setParameter('userId', $userId)
                ->setParameter('orgId', $orgId)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        public function findAssociationRequestsInitiatedByUser($userId)
        {
            return $this->createQueryBuilder('u')
                ->where('u.initiatedBy = :userId')
                ->setParameter('userId', $userId)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        
        public function findAssociationRequestsByClubAndStatus($clubId, $status)
        {
            return $this->createQueryBuilder('u')
                ->where('u.organization = :clubId')
                ->andWhere('u.status = :status')
                ->setParameter('clubId', $clubId)
                ->setParameter('status', $status)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        
        public function findAssociationRequestsByUserAndStatus($userId, $status)
        {
            return $this->createQueryBuilder('u')
                ->where('u.user = :userId')
                ->andWhere('u.status = :status')
                ->setParameter('userId', $userId)
                ->setParameter('status', $status)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        
        public function countAssociationRequestsByStatus($status)
        {
            return $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.status = :status')
                ->setParameter('status', $status)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getSingleScalarResult();
        }
        
        public function findAssociationRequestsByRole($role)
        {
            return $this->createQueryBuilder('u')
                ->where('u.role = :role')
                ->setParameter('role', $role)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        
        public function findUsersOfOrganization($orgId)
        {
            $orderedResults = $this->createQueryBuilder('u')
                ->where('u.org = :orgId')
                ->setParameter('orgId', $orgId)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
            
            shuffle($orderedResults);
            
            return $orderedResults;
        }
        
        public function findMembersOfOrganization($orgId)
        {
            $orderedResults = $this->createQueryBuilder('u')
                ->where('u.org = :orgId')
                ->andWhere('u.status = 1')
                ->setParameter('orgId', $orgId)
                ->orderBy('u.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
            
            shuffle($orderedResults);
            
            return $orderedResults;
        }
    }
