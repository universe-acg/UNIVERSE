<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\Order;
    use App\Entity\OrderElement;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    /**
     * @method OrderElement|null find($id, $lockMode = null, $lockVersion = null)
     * @method OrderElement|null findOneBy(array $criteria, array $orderBy = null)
     * @method OrderElement[]    findAll()
     * @method OrderElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class OrderElementRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, OrderElement::class);
        }
        
        /**
         * Get EventTickets associated with a specific Order.
         *
         * @param Order $order The Order entity.
         *
         * @return array An array of EventTicket entities associated with the Order.
         */
        public function findEventTicketsByOrder(Order $order)
        {
            return $this->createQueryBuilder('oe')
                ->select('oe')
                ->join('oe.eventticket', 'et')
                ->where('oe.order = :order')
                ->setParameter('order', $order)
                ->getQuery()
                ->getResult();
        }
        
        public function findByExampleField($value)
        {
            return $this->createQueryBuilder('o')
                ->andWhere('o.exampleField = :val')
                ->setParameter('val', $value)
                ->orderBy('o.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        }
        
        public function findOneBySomeField($value): ?OrderElement
        {
            return $this->createQueryBuilder('o')
                ->andWhere('o.exampleField = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        }
    }
