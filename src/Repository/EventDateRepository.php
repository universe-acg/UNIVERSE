<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\EventDate;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    class EventDateRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, EventDate::class);
        }
        
        public function findById($id)
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        public function getEventDates($reference, $organizer, $event, $limit, $count)
        {
            $qb = $this->createQueryBuilder("e");
            if ($count) {
                $qb->select("COUNT(e)");
            } else {
                $qb->select("e");
            }
            if ($reference !== "all") {
                $qb->andWhere("e.reference = :reference")->setParameter("reference", $reference);
            }
            if ($event !== "all" || $organizer !== "all") {
                $qb->leftJoin("e.event", "event");
            }
            if ($organizer !== "all") {
                $qb->leftJoin("event.organizer", "organizer");
                $qb->andWhere("organizer.slug = :organizer")->setParameter("organizer", $organizer);
            }
            if ($event !== "all") {
                $qb->andWhere("slug = :event")->setParameter("event", $event);
            }
            $qb->orderBy("e.startdate", "ASC");
            if ($limit !== "all") {
                $qb->setMaxResults($limit);
            }
            return $qb;
        }
        
        public function findByReference($reference)
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.reference = :reference')
                ->setParameter('reference', $reference)
                ->getQuery()
                ->getOneOrNullResult();
        }
    }
