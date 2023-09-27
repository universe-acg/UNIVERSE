<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\BlogPost;
    use App\Entity\Organizer;
    use App\Entity\User;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    /**
     * @method Organizer|null find($id, $lockMode = null, $lockVersion = null)
     * @method Organizer|null findOneBy(array $criteria, array $orderBy = null)
     * @method Organizer[]    findAll()
     * @method Organizer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class OrganizerRepository extends ServiceEntityRepository
    {
        
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Organizer::class);
        }
        
        // Add this custom method to search organizers by keyword and category
        public function searchByKeywordAndCategory($keyword, $category)
        {
            $queryBuilder = $this->createQueryBuilder('o')
                ->andWhere('o.name LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
            
            if (!empty($category)) {
                $queryBuilder
                    ->andWhere('o.category = :category')
                    ->setParameter('category', $category);
            }
            
            return $queryBuilder
                ->getQuery()
                ->getResult();
        }
        
        /**
         * @return Organizer[] Returns an array of Organizer objects
         */
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
        
        public function findOneBySomeField($value): ?Organizer
        {
            return $this->createQueryBuilder('o')
                ->andWhere('o.exampleField = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        public function findCategoryById($organizerId)
        {
            return $this->createQueryBuilder('o')
                ->select('o.category')
                ->where('o.id = :id')
                ->setParameter('id', $organizerId)
                ->getQuery()
                ->getSingleScalarResult();
        }
        
        // Add this method to retrieve news published by the organizer.
        public function findNewsByUser(User $user)
        {
            return $this->createQueryBuilder('o')
                ->select('n')
                ->from(BlogPost::class, 'n')
                ->where('n.user = :user')
                ->andWhere('n.hidden = 0')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
        }
    }
