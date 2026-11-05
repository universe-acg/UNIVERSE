<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Repository;

    use App\Entity\HelpCenterCategory;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    class HelpCenterCategoryRepository extends ServiceEntityRepository
    {

        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, HelpCenterCategory::class);
        }

        public function getHelpCenterCategories($parent, $hidden, $keyword, $slug, $limit, $order, $sort)
        {
            $qb = $this->createQueryBuilder("c");
            $qb->select("c");
            if ($parent !== "all") {
                if ($parent === "none") {
                    $qb->andWhere("c.parent IS NULL");
                } else if ($parent === "notnull") {
                    $qb->andWhere("c.parent IS NOT NULL");
                } else {
                    $qb->leftJoin("c.parent", "parentcategory");
                    $qb->andWhere("parentcategory.slug = :parentcategory");
                    $qb->setParameter("parentcategory", $parent);
                }
            }
            if ($hidden !== "all") {
                $qb->andWhere("c.hidden = :hidden AND c.deletedAt IS NULL")->setParameter("hidden", $hidden);
            }
            if ($keyword !== "all") {
                $qb->andWhere("c.name LIKE :keyword or :keyword LIKE c.name")->setParameter("keyword", "%" . $keyword . "%");
            }
            if ($slug !== "all") {
                $qb->andWhere("c.slug = :slug")->setParameter("slug", $slug);
            }
            if ($limit !== "all") {
                $qb->setMaxResults($limit);
            }

            if ($order == "articlescount") {

                $qb->addSelect("SIZE(c.articles) AS HIDDEN articlescount");
                $qb->orderBy("articlescount", "DESC");

            } else {
                $field=in_array($order,['name','createdAt','updatedAt'],true) ? $order : 'name';
                $qb->orderBy('c.'.$field,strtoupper($sort)==='DESC'?'DESC':'ASC');
            }

            return $qb;
        }
    }
