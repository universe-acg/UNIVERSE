<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Repository;

    use App\Entity\BlogPostCategory;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    class BlogPostCategoryRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, BlogPostCategory::class);
        }

        public function getBlogPostCategories($hidden, $keyword, $slug, $limit)
        {
            $qb = $this->createQueryBuilder("b");
            $qb->select("b");
            if ($hidden !== "all") {
                $qb->andWhere("b.hidden = :hidden")->setParameter("hidden", $hidden);
            }
            if ($keyword !== "all") {
                $qb->andWhere("b.name LIKE :keyword or :keyword LIKE b.name")->setParameter("keyword", "%" . $keyword . "%");
            }
            if ($slug !== "all") {
                $qb->andWhere("b.slug = :slug")->setParameter("slug", $slug);
            }
            if ($limit !== "all") {
                $qb->setMaxResults($limit);
            }
//        if ($order == "blogpostscount") {

            $qb->addSelect("SIZE(b.blogposts) AS HIDDEN blogpostscount");
            $qb->orderBy("blogpostscount", "DESC");

//        } else {
//            $qb->orderBy($order, $sort);
//        }
            return $qb;
        }

        public function findByName($name)
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult();
        }
    }
