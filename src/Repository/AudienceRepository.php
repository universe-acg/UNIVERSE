<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Audience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AudienceRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Audience::class);
    }

    public function getAudiences($hidden, $keyword, $slug, $limit, $sort, $order)
    {
        $qb = $this->createQueryBuilder("a");
        $qb->select("a");

        if ($hidden !== "all") {
            $qb->andWhere("a.hidden = :hidden")->setParameter("hidden", $hidden);
        }
        if ($keyword !== "all") {
            $qb->andWhere("a.name LIKE :keyword OR :keyword LIKE a.name")->setParameter("keyword", "%" . $keyword . "%");
        }
        if ($slug !== "all") {
            $qb->andWhere("a.slug = :slug")->setParameter("slug", $slug);
        }
        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }
        $qb->orderBy("a." . $sort, $order); // Fix the "orderBy" statement.

        // Execute the query and return the results.
        return $qb;
    }
}
