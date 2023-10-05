<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\Category;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    class CategoryRepository extends ServiceEntityRepository
    {
        
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Category::class);
        }
        
        // Add your custom method to find a category by name
        public function findByName($name)
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        public function getCategories($hidden, $keyword, $slug, $featured, $limit, $sort, $order)
        {
            $qb = $this->createQueryBuilder("c");
            $qb->select("DISTINCT c");
            
            if ($hidden !== "all") {
                $qb->andWhere("c.hidden = :hidden")->setParameter("hidden", $hidden);
            }
            
            if ($keyword !== "all") {
                $qb->andWhere("c.name LIKE :keyword or :keyword LIKE c.name")->setParameter("keyword", "%" . $keyword . "%");
            }
            
            if ($slug !== "all") {
                $qb->andWhere("c.slug = :slug")->setParameter("slug", $slug);
            }
            
            if ($featured !== "all") {
                $qb->andWhere("c.featured = :featured")->setParameter("featured", $featured);
                
                if ($featured === true) {
                    $qb->orderBy("c.featuredorder", "ASC");
                }
            }
            
            if ($limit !== "all") {
                $qb->setMaxResults($limit);
            }
            
            if ($sort !== "all" && $order !== "all") {
                $qb->orderBy("c." . $sort, $order);
            }
            
            return $qb;
        }
        
        // Add a method to retrieve a category by its ID
        public function getCategoryById($categoryId)
        {
            return $this->createQueryBuilder('c')
                ->where('c.id = :id')
                ->setParameter('id', $categoryId)
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        public function getByUserId($userId)
        {
            return $this->createQueryBuilder('c')
                ->innerJoin('c.user', 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $userId)
                ->getQuery()
                ->getResult();
        }
    }
