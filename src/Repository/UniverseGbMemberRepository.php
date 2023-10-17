<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\UniverseGbMember;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    class UniverseGbMemberRepository extends ServiceEntityRepository
    {
        
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, UniverseGbMember::class);
        }
        
        public function getById($id)
        {
            return $this->find($id);
        }
        
        public function getByOrg($orgId)
        {
            return $this->findBy(['org' => $orgId]);
        }
        
        public function getByAttendee($attendeeId)
        {
            return $this->findBy(['attendee' => $attendeeId]);
        }
        
        public function getAll()
        {
            return $this->findAll();
        }
        
        public function updateByOrg($orgId, $data)
        {
            $members = $this->findBy(['org' => $orgId]);
            foreach ($members as $member) {
                // Update properties based on $data
                // Example: $member->setTitle($data['title']);
                // ... Update other properties ...
                $this->getEntityManager()->persist($member);
            }
            $this->getEntityManager()->flush();
        }
        
        public function deleteById($id)
        {
            $member = $this->find($id);
            if ($member) {
                $this->getEntityManager()->remove($member);
                $this->getEntityManager()->flush();
            }
        }
        
        public function updateById($id, $data)
        {
            $member = $this->find($id);
            if ($member) {
                // Update properties based on $data
                // Example: $member->setTitle($data['title']);
                // ... Update other properties ...
                $this->getEntityManager()->persist($member);
                $this->getEntityManager()->flush();
            }
        }
    }