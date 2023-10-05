<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\Event;
    use App\Entity\Order;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    class OrderRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Order::class);
        }
        
        public function findByEventId($eventId)
        {
            return $this->createQueryBuilder('o')
                ->join('o.orderelements', 'oe')
                ->join('oe.ticketsReservations', 'oet')
                ->join('oet.eventticket', 'et')
                ->join('et.eventdate', 'ed')
                ->join('ed.event', 'e')
                ->where('e.id = :eventId')
                ->setParameter('eventId', $eventId)
                ->getQuery()
                ->getResult();
        }
        
        public function countOrdersWithStatusAboveOneForEventTicket($eventTicket)
        {
            $qb = $this->createQueryBuilder('o')
                ->select('COUNT(o)')
                ->leftJoin('o.orderelements', 'orderelement')
                ->leftJoin('orderelement.eventticket', 'eventticket')
                ->where('eventticket = :eventTicket')
                ->andWhere('o.status >= 1')
                ->setParameter('eventTicket', $eventTicket);
            
            return $qb->getQuery()->getSingleScalarResult();
        }
        
        public function getOrdersForEvent($criterias, Event $event)
        {
            $qb = $this->createQueryBuilder("o");
            
            // Select clause
            $qb->select("o");
            
            // Add filter for the specific event
            $qb->leftJoin("o.orderelements", "orderelement")
                ->leftJoin("orderelement.eventticket", "eventticket")
                ->leftJoin("eventticket.eventdate", "eventdate")
                ->leftJoin("eventdate.event", "event")
                ->andWhere("event = :event")
                ->setParameter("event", $event);
            
            $qb->orderBy("o.createdAt", "DESC");
            
            return $qb;
        }
        
        public function getOrders($status, $user, $organizer, $event, $eventDate, $eventTicket, $reference, $upcomingtickets, $datefrom, $dateto, $sort, $order, $limit, $count, $ordersQuantityByDateStat, $sumOrderElements)
        {
            $qb = $this->createQueryBuilder("o");
            if ($count) {
                $qb->select("COUNT(o)");
            } elseif ($ordersQuantityByDateStat) {
                $qb->select("SUM(orderelement.quantity), DATE(o.createdAt) as dateCreatedAt");
                $qb->groupBy("dateCreatedAt");
            } elseif ($sumOrderElements) {
                $qb->select("SUM(orderelement.quantity)");
            } else {
                $qb->select("o");
            }
            if ($status !== "all") {
                $qb->andWhere("o.status >= :status")->setParameter("status", $status);
            }
            if ($user !== "all") {
                $qb->leftJoin("o.user", "user");
                $qb->andWhere("user.slug = :user")->setParameter("user", $user);
            }
            if ($organizer !== "all" || $upcomingtickets !== "all" || $event !== "all" || $eventDate !== "all" || $eventTicket !== "all" || $ordersQuantityByDateStat || $sumOrderElements) {
                $qb->leftJoin("o.orderelements", "orderelement");
                $qb->leftJoin("orderelement.eventticket", "eventticket");
                $qb->leftJoin("eventticket.eventdate", "eventdate");
            }
            if ($organizer !== "all" || $event !== "all") {
                $qb->leftJoin("eventdate.event", "event");
            }
            if ($organizer !== "all") {
                $qb->leftJoin("event.organizer", "organizer");
                $qb->andWhere("organizer.slug = :organizer")->setParameter("organizer", $organizer);
            }
            if ($event !== "all") {
                $qb->andWhere("event.slug = :event")->setParameter("event", $event);
            }
            if ($eventDate !== "all") {
                $qb->andWhere("eventdate.reference = :eventdate")->setParameter("eventdate", $eventDate);
            }
            if ($eventTicket !== "all") {
                $qb->andWhere("eventticket.reference = :eventticket")->setParameter("eventticket", $eventTicket);
            }
            if ($reference !== "all") {
                $qb->andWhere("o.reference = :reference")->setParameter("reference", $reference);
            }
            if ($datefrom !== "all") {
                $qb->andWhere("o.createdAt >= :datefrom")->setParameter("datefrom", $datefrom);
            }
            if ($dateto !== "all") {
                $qb->andWhere("o.createdAt <= :dateto")->setParameter("dateto", $dateto);
            }
            if ($upcomingtickets !== "all") {
                if ($upcomingtickets === 1) {
                    $qb->andWhere("eventdate.startdate >= NOW()");
                } else if ($upcomingtickets === 0) {
                    $qb->andWhere("eventdate.startdate < NOW()");
                }
            }
            if ($limit !== "all") {
                $qb->setMaxResults($limit);
            }
            if ($sort && !$ordersQuantityByDateStat) {
                $qb->orderBy("o." . $sort, $order);
            }
            return $qb;
        }
        
        // Add a custom method to retrieve orders by user
        public function getOrdersByUser($user)
        {
            $qb = $this->createQueryBuilder('o')
                ->where('o.user = :user')
                ->setParameter('user', $user)
                ->getQuery();
            
            return $qb->getResult();
        }
    }
