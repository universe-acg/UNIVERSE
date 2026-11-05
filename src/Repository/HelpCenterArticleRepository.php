<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\HelpCenterArticle;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    class HelpCenterArticleRepository extends ServiceEntityRepository
    {
        
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, HelpCenterArticle::class);
        }
        
        public function getHelpCenterArticles($selecttags, $hidden, $featured, $keyword, $slug, $category, $limit, $sort, $order, $otherthan)
        {
            $qb = $this->createQueryBuilder("a");
            
            if (!$selecttags) {
                $qb->select("a");
                
                if ($hidden !== "all") {
                    $qb->andWhere("a.hidden = :hidden")->setParameter("hidden", $hidden);
                }
                if ($featured !== "all") {
                    $qb->andWhere("a.featured = :featured")->setParameter("featured", $featured);
                }
                if ($keyword !== "all") {
                    $qb->andWhere($qb->expr()->orX(
                        $qb->expr()->like('a.title', ':keyword'),
                        $qb->expr()->like('a.content', ':keyword'),
                        $qb->expr()->like('a.tags', ':keyword')
                    ))->setParameter("keyword", "%" . $keyword . "%");
                }
                if ($slug !== "all") {
                    $qb->andWhere("a.slug = :slug")->setParameter("slug", $slug);
                }
                $qb->leftJoin("a.category", "category");
                if ($hidden !== "all") {
                    $qb->andWhere("category.hidden = false");
                }
                if ($category !== "all") {
                    $qb->andWhere("category.slug = :category")->setParameter("category", $category);
                }
                if ($limit !== "all") {
                    $qb->setMaxResults($limit);
                }
                if ($otherthan !== "all") {
                    $qb->andWhere("a.slug != :otherthan")->setParameter("otherthan", $otherthan);
                }
                $field = in_array($order, ['title','createdAt','updatedAt','views'], true) ? $order : 'title';
                $direction = strtoupper($sort) === 'ASC' ? 'ASC' : 'DESC';
                $qb->orderBy('a.'.$field, $direction)->addOrderBy('a.id', 'ASC');
            } else {
                $qb->select("SUBSTRING_INDEX(GROUP_CONCAT(a.tags SEPARATOR ','), ',', 8)");
            }
            
            return $qb;
        }
    }
