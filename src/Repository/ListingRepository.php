<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Listing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ListingRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Listing::class);
    }

    public function getListingsByOrganizerId($organizerId)
    {
        $qb = $this->createQueryBuilder("l")
            ->join("l.organizer", "organizer")
            ->andWhere("organizer.id = :organizerId")
            ->setParameter("organizerId", $organizerId)
            ->orderBy("l.updatedAt", "DESC");
        return $qb->getQuery()->getResult();
    }

    public function getListingsForEvent($eventId, $limit = null)
    {
        $qb = $this->createQueryBuilder("l");

        // Join with events table
        $qb->leftJoin('l.event', 'e');

        // Select listings associated with the specified event
        $qb->andWhere("e.id = :eventId")->setParameter("eventId", $eventId);

        // Optionally limit the number of results
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        // Order by createdAt in descending order
        $qb->orderBy("l.createdAt", "DESC");

        // Execute query and return result
        return $qb->getQuery()->getResult();
    }


    public function getListings($hidden, $keyword, $slug, $limit, $count)
    {
        $qb = $this->createQueryBuilder("l");
        if ($count) {
            $qb->select("COUNT(l)");
        } else {
            $qb->select("l");
        }
//        if ($hidden !== "all") {
//            $qb->andWhere("v.hidden = :hidden")->setParameter("hidden", $hidden);
//        }
//        if ($keyword !== "all") {
//            $qb->andWhere("v.name LIKE :keyword or :keyword LIKE v.name or v.description LIKE :keyword or :keyword LIKE v.description")->setParameter("keyword", "%" . $keyword . "%");
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

    public function getMyListings($hidden, $keyword, $slug, $limit, $count, $organizerid)
    {
        $qb = $this->createQueryBuilder("l");
        if ($count) {
            $qb->select("COUNT(l)");
        } else {
            $qb->select("l");
        }
//        if ($hidden !== "all") {
//            $qb->andWhere("v.hidden = :hidden")->setParameter("hidden", $hidden);
//        }
//        if ($keyword !== "all") {
//            $qb->andWhere("v.name LIKE :keyword or :keyword LIKE v.name or v.description LIKE :keyword or :keyword LIKE v.description")->setParameter("keyword", "%" . $keyword . "%");
//        }
//
        if ($slug !== "all") {
            $qb->andWhere("l.slug = :slug")->setParameter("slug", $slug);
        }
//        if ($limit !== "all") {
//            $qb->setMaxResults($limit);
//        }
        $qb->andWhere("l.organizer = :organizer")->setParameter("organizer", $organizerid);
        $qb->orderBy("l.createdAt", "DESC");
        return $qb->getQuery()->getResult();
    }


    public function getListingssql($hidden, $keyword, $slug, $limit, $count)
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
