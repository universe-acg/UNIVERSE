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
        
        public function searchByKeywordAndCategory($keyword, $category)
        {
            $queryBuilder = $this->createQueryBuilder('o')
                ->andWhere('o.name LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
            
            if ($category != "all") {
                $queryBuilder
                    ->andWhere('o.category = :category')
                    ->setParameter('category', $category);
            }
            
            if ($sortedby !== "all") {
                switch ($sortedby) {
                    case 'created_asc':
                        $qb->orderBy("b.createdAt", 'ASC');
                        break;
                    case 'created_desc':
                        $qb->orderBy("b.createdAt", 'DESC');
                        break;
                    case 'updated_asc':
                        $qb->orderBy("b.updatedAt", 'ASC');
                        break;
                    case 'updated_desc':
                        $qb->orderBy("b.updatedAt", 'DESC');
                        break;
                    case 'title_asc':
                        $qb->orderBy("b.name", 'ASC');
                        break;
                    case 'title_desc':
                        $qb->orderBy("b.name", 'DESC');
                        break;
                    default:
                        // Default sorting by created date in descending order
                        $qb->orderBy("b.createdAt", 'DESC');
                        break;
                }
            }
            
            return $queryBuilder
                ->getQuery()
                ->getResult();
        }
        
        public function findUsersFollowingOrganizer(Organizer $organizer)
        {
            return $this->createQueryBuilder('o')
                ->select('u')
                ->from(User::class, 'u')
                ->innerJoin('u.following', 'f')
                ->where('f.organizer = :organizer')
                ->setParameter('organizer', $organizer)
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
        
        public function getCreators($level, $keyword, $category, $limit, $sortedby)
        {
            $qb = $this->createQueryBuilder("o");
            
            $this->applyKeywordFilter($qb, $keyword);
            $this->applyLevelFilter($qb, $level);
            $this->applyCategoryFilter($qb, $category);
            $this->applyLimit($qb, $limit);
            $this->applySorting($qb, $sortedby);
            
            return $qb;
        }
        
        private function applyKeywordFilter($qb, $keyword)
        {
            if ($keyword !== "all") {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like("o.name", ":keyword"),
                        $qb->expr()->like("o.description", ":keyword"),
                        $qb->expr()->like("o.instagram", ":keyword"),
                        $qb->expr()->like("o.email", ":keyword")
                    )
                )->setParameter("keyword", "%" . $keyword . "%");
            }
        }
        
        private function applyLevelFilter($qb, $level)
        {
            if ($level !== "all") {
                if ($level < 40) {
                    $qb->leftJoin("o.user", "u")
                        ->andWhere(
                            $qb->expr()->andX(
                                $qb->expr()->gte("u.points", $level),
                                $qb->expr()->lt("u.points", $level + 9)
                            )
                        );
                } else {
                    $qb->leftJoin("o.user", "u")
                        ->andWhere("u.points >= :level")
                        ->setParameter("level", $level);
                }
            }
        }
        
        private function applyCategoryFilter($qb, $category)
        {
            if ($category !== "all") {
                $qb->andWhere("o.category = :category")
                    ->setParameter("category", $category);
            }
        }
        
        private function applyLimit($qb, $limit)
        {
            if ($limit !== "all") {
                $qb->setMaxResults($limit);
            }
        }
        
        private function applySorting($qb, $sortedby)
        {
            if ($sortedby !== "all") {
                switch ($sortedby) {
                    case 'created_asc':
                        $qb->orderBy("o.createdAt", 'ASC');
                        break;
                    case 'created_desc':
                        $qb->orderBy("o.createdAt", 'DESC');
                        break;
                    case 'updated_asc':
                        $qb->orderBy("o.updatedAt", 'ASC');
                        break;
                    case 'updated_desc':
                        $qb->orderBy("o.updatedAt", 'DESC');
                        break;
                    case 'title_asc':
                        $qb->orderBy("o.name", 'ASC');
                        break;
                    case 'title_desc':
                        $qb->orderBy("o.name", 'DESC');
                        break;
                    default:
                        // Default sorting by created date in descending order
                        $qb->orderBy("o.createdAt", 'DESC');
                        break;
                }
            }
        }
        
        public function getById($id)
        {
            return $this->createQueryBuilder('o')
                ->andWhere('o.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
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
