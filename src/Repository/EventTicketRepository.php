<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\EventTicket;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\ORM\Query\ResultSetMappingBuilder;
    use Doctrine\Persistence\ManagerRegistry;
    
    class EventTicketRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, EventTicket::class);
        }
        
        public function countOrdersForTicket(EventTicket $ticket)
        {
            $em = $this->getEntityManager();
            $rsm = new ResultSetMappingBuilder($em);
            $rsm->addScalarResult('orderCount', 'orderCount');
            
            $query = $em->createNativeQuery('
        SELECT COUNT(DISTINCT oe.order_id) AS orderCount
        FROM universe_event_date_ticket et
        LEFT JOIN universe_order_element oe ON et.id = oe.eventticket_id
        WHERE et.id = :ticketId
    ', $rsm);
            
            $query->setParameter('ticketId', $ticket->getId());
            
            return (int)$query->getSingleScalarResult();
        }
        
        public function getEventTickets($reference, $organizer, $event, $eventdate, $limit)
        {
            $qb = $this->createQueryBuilder("e");
            $qb->select("e");
            if ($reference !== "all") {
                $qb->andWhere("e.reference = :reference")->setParameter("reference", $reference);
            }
            if ($event !== "all" || $organizer !== "all" || $eventdate !== "all") {
                $qb->leftJoin("e.eventdate", "eventdate");
            }
            if ($event !== "all" || $organizer !== "all") {
                $qb->leftJoin("eventdate.event", "event");
            }
            if ($organizer !== "all") {
                $qb->leftJoin("event.organizer", "organizer");
                $qb->andWhere("organizer.slug = :organizer")->setParameter("organizer", $organizer);
            }
            if ($event !== "all") {
                $qb->andWhere("event.slug = :event")->setParameter("event", $event);
            }
            if ($eventdate !== "all") {
                $qb->andWhere("eventdate.reference = :eventdate")->setParameter("eventdate", $eventdate);
            }
            $qb->orderBy("e.id", "ASC");
            if ($limit !== "all") {
                $qb->setMaxResults($limit);
            }
            return $qb;
        }
        
        public function findById($id)
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }
    }
