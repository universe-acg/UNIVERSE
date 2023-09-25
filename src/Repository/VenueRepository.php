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

    public function getVenues($organizer, $hidden, $keyword, $country, $venuetypes, $directory, $slug, $limit, $minseatedguests, $maxseatedguests, $minstandingguests, $maxstandingguests, $count, $organizerEnabled)
    {
        $qb = $this->createQueryBuilder("v");
        if ($count) {
            $qb->select("COUNT(v)");
        } else {
            $qb->select("v");
        }
        //if ($organizer !== "all" || $organizerEnabled !== "all") {
        if ($organizer !== "all") {
            $qb->innerJoin("v.organizer", "organizer");
        }
        if ($organizer !== "all") {
            $qb->andWhere("organizer.slug = :organizer")->setParameter("organizer", $organizer);
        }
        /*if ($organizerEnabled !== "all") {
            $qb->innerJoin("organizer.user", "user");
            $qb->andWhere("user.enabled = :userEnabled")->setParameter("userEnabled", $organizerEnabled);
        }*/

        if ($hidden !== "all") {
            $qb->andWhere("v.hidden = :hidden")->setParameter("hidden", $hidden);
        }
        if ($keyword !== "all") {
            $qb->andWhere("v.name LIKE :keyword or :keyword LIKE v.name or v.description LIKE :keyword or :keyword LIKE v.description")->setParameter("keyword", "%" . $keyword . "%");
        }
        if ($directory !== "all") {
            $qb->andWhere("v.listedondirectory = :listedondirectory")->setParameter("listedondirectory", $directory);
        }
        if ($slug !== "all") {
            $qb->andWhere("v.slug = :slug")->setParameter("slug", $slug);
        }
        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }
        if ($minseatedguests !== "all") {
            $qb->andWhere("v.seatedguests >= :minseatedguests")->setParameter("minseatedguests", $minseatedguests);
        }
        if ($maxseatedguests !== "all") {
            $qb->andWhere("v.seatedguests <= :maxseatedguests")->setParameter("maxseatedguests", $maxseatedguests);
        }
        if ($minstandingguests !== "all") {
            $qb->andWhere("v.standingguests >= :minstandingguests")->setParameter("minstandingguests", $minstandingguests);
        }
        if ($maxseatedguests !== "all") {
            $qb->andWhere("v.standingguests <= :maxstandingguests")->setParameter("maxstandingguests", $maxstandingguests);
        }
        $qb->orderBy("v.createdAt", "DESC");
        return $qb;
    }

}
