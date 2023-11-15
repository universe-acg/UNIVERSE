<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Live;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LiveRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Live::class);
    }

    public function getLives($hidden, $keyword, $slug, $limit, $count)
    {
        $qb = $this->createQueryBuilder("l");
        if ($count) {
            $qb->select("COUNT(l)");
        } else {
            $qb->select("l");
        }
//        if ($hidden !== "all") {
//            $qb->andWhere("l.hidden = :hidden")->setParameter("hidden", $hidden);
//        }
//        if ($keyword !== "all") {
//            $qb->andWhere("l.name LIKE :keyword or :keyword LIKE l.name or l.description LIKE :keyword or :keyword LIKE l.description")->setParameter("keyword", "%" . $keyword . "%");
//        }
//
        if ($slug !== "all") {
            $qb->andWhere("l.slug = :slug")->setParameter("slug", $slug);
        }
//        if ($limit !== "all") {
//            $qb->setMaxResults($limit);
//        }
        $qb->orderBy("l.createdAt", "DESC");
        return $qb->getQuery()->getResult();
    }


    public function getLivessql($hidden, $keyword, $slug, $limit, $count)
    {
        $qb = $this->createQueryBuilder("l");
        if ($count) {
            $qb->select("COUNT(l)");
        } else {
            $qb->select("l");
        }

        if ($hidden !== "all") {
            $qb->andWhere("l.hidden = :hidden")->setParameter("hidden", $hidden);
        }
        if ($keyword !== "all") {
            $qb->andWhere("l.name LIKE :keyword or :keyword LIKE l.name or l.description LIKE :keyword or :keyword LIKE l.description")->setParameter("keyword", "%" . $keyword . "%");
        }

        if ($slug !== "all") {
            $qb->andWhere("l.slug = :slug")->setParameter("slug", $slug);
        }
        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }
        $qb->orderBy("l.createdAt", "DESC");
        return $qb;
    }
}
