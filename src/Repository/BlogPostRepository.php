<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\BlogPost;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    class BlogPostRepository extends ServiceEntityRepository
    {
        
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, BlogPost::class);
        }
        
        public function getBlogPosts($selecttags, $hidden, $keyword, $slug, $category, $limit, $sort, $order, $otherthan)
        {
            $qb = $this->createQueryBuilder("b");
            
            if ($selecttags !== "all") {
                $qb->andWhere(
                    $qb->expr()->like("b.tags", ":keyword")
                )->setParameter("keyword", "%" . $selecttags . "%");
            }
            
            if ($hidden !== "all") {
                $qb->andWhere("b.hidden = :hidden")->setParameter("hidden", $hidden);
            }
            
            if ($keyword !== "all") {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like("b.name", ":keyword"),
                        $qb->expr()->like("b.content", ":keyword"),
                        $qb->expr()->like("b.tags", ":keyword")
                    )
                )->setParameter("keyword", "%" . $keyword . "%");
            }
            
            if ($slug !== "all") {
                $qb->andWhere("b.slug = :slug")->setParameter("slug", $slug);
            }
            
            if ($category !== "all") {
                $qb->leftJoin("b.category", "category")
                    ->andWhere("category.slug = :category")
                    ->setParameter("category", $category);
            }
            
            if ($limit !== "all") {
                $qb->setMaxResults($limit);
            }
            
            if ($otherthan !== "all") {
                $qb->andWhere("b.id != :otherthan")->setParameter("otherthan", $otherthan);
            }
            
            $qb->orderBy("b." . $order, $sort);
            
            return $qb;
        }
    }
