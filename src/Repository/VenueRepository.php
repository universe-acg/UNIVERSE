<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Venue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VenueRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Venue::class);
    }

    public function getVenues($hidden, $keyword, $slug, $limit, $count)
    {
        $qb = $this->createQueryBuilder("v");
        if ($count) {
            $qb->select("COUNT(v)");
        } else {
            $qb->select("v");
        }
//        if ($hidden !== "all") {
//            $qb->andWhere("v.hidden = :hidden")->setParameter("hidden", $hidden);
//        }
//        if ($keyword !== "all") {
//            $qb->andWhere("v.name LIKE :keyword or :keyword LIKE v.name or v.description LIKE :keyword or :keyword LIKE v.description")->setParameter("keyword", "%" . $keyword . "%");
//        }
//
        if ($slug !== "all") {
            $qb->andWhere("v.slug = :slug")->setParameter("slug", $slug);
        }
//        if ($limit !== "all") {
//            $qb->setMaxResults($limit);
//        }
        $qb->orderBy("v.createdAt", "DESC");
        return $qb->getQuery()->getResult();
    }


    public function getVenuessql($hidden, $keyword, $slug, $limit, $count)
    {
        $qb = $this->createQueryBuilder("v");
        if ($count) {
            $qb->select("COUNT(v)");
        } else {
            $qb->select("v");
        }

        if ($hidden !== "all") {
            $qb->andWhere("v.hidden = :hidden")->setParameter("hidden", $hidden);
        }
        if ($keyword !== "all") {
            $qb->andWhere("v.name LIKE :keyword or :keyword LIKE v.name or v.description LIKE :keyword or :keyword LIKE v.description")->setParameter("keyword", "%" . $keyword . "%");
        }

        if ($slug !== "all") {
            $qb->andWhere("v.slug = :slug")->setParameter("slug", $slug);
        }
        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }
        $qb->orderBy("v.createdAt", "DESC");
        return $qb;
    }

}
