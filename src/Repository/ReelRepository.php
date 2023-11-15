<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Reel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReelRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reel::class);
    }

    public function getReels($hidden, $keyword, $slug, $limit, $count)
    {
        $qb = $this->createQueryBuilder("r");
        if ($count) {
            $qb->select("COUNT(r)");
        } else {
            $qb->select("r");
        }
//        if ($hidden !== "all") {
//            $qb->andWhere("r.hidden = :hidden")->setParameter("hidden", $hidden);
//        }
//        if ($keyword !== "all") {
//            $qb->andWhere("r.name LIKE :keyword or :keyword LIKE r.name or r.description LIKE :keyword or :keyword LIKE r.description")->setParameter("keyword", "%" . $keyword . "%");
//        }
//
        if ($slug !== "all") {
            $qb->andWhere("r.slug = :slug")->setParameter("slug", $slug);
        }
//        if ($limit !== "all") {
//            $qb->setMaxResults($limit);
//        }
        $qb->orderBy("r.createdAt", "DESC");
        return $qb->getQuery()->getResult();
    }


    public function getReelssql($hidden, $keyword, $slug, $limit, $count)
    {
        $qb = $this->createQueryBuilder("r");
        if ($count) {
            $qb->select("COUNT(l)");
        } else {
            $qb->select("r");
        }

        if ($hidden !== "all") {
            $qb->andWhere("r.hidden = :hidden")->setParameter("hidden", $hidden);
        }
        if ($keyword !== "all") {
            $qb->andWhere("r.name LIKE :keyword or :keyword LIKE r.name or r.description LIKE :keyword or :keyword LIKE r.description")->setParameter("keyword", "%" . $keyword . "%");
        }

        if ($slug !== "all") {
            $qb->andWhere("r.slug = :slug")->setParameter("slug", $slug);
        }
        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }
        $qb->orderBy("r.createdAt", "DESC");
        return $qb;
    }
}
