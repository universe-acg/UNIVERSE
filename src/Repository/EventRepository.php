<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }


    public function findBySlug($slug)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByName($name)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findById($id)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

//        public function getEvents($category, $venue, $country, $location, $organizer, $keyword, $slug, $freeonly, $onlineonly, $pricemin, $pricemax, $audience, $startdate, $startdatemin, $startdatemax, $published, $elapsed, $organizerEnabled, $addedtofavoritesby, $onsalebypos, $canbescannedby, $isOnHomepageSlider, $otherthan, $sort, $order, $limit, $count): QueryBuilder
//        {
//            $qb = $this->createQueryBuilder("e");
//            if ($count) {
//                $qb->select("COUNT(e)");
//            } else {
//                $qb->select("DISTINCT e");
//            }
//
//            if ($category !== "all") {
//                $qb->leftJoin("e.category", "category");
//                $qb->andWhere("category.slug = :category")->setParameter("category", $category);
//            }
//
//            // Update references to translation fields to use fields in the "event" table
//            if ($keyword !== "all") {
//                $qb->andWhere("e.name LIKE :keyword OR e.description LIKE :keyword OR e.tags LIKE :keyword OR e.artists LIKE :keyword")->setParameter("keyword", "%" . $keyword . "%");
//            }
//
//            if ($slug !== "all") {
//                $qb->andWhere("e.slug = :slug")->setParameter("slug", $slug);
//            }
//
//            // Other conditions and joins...
//
//            $qb->orderBy($sort, $order);
//
//            if ($limit !== "all") {
//                $qb->setMaxResults($limit);
//            }
//
//            return $qb;
//        }

    public function getEvents($category, $venue, $country, $location, $organizer, $keyword, $slug, $freeonly, $onlineonly, $pricemin, $pricemax, $audience, $startdate, $startdatemin, $startdatemax, $published, $elapsed, $organizerEnabled, $addedtofavoritesby, $isOnHomepageSlider, $otherthan, $sort, $order, $limit, $count): QueryBuilder
    {
        $qb = $this->createQueryBuilder("e");
        if ($count) {
            $qb->select("COUNT(e)");
        } else {
            $qb->select("DISTINCT e");
        }

        if ($category !== "all") {
            $qb->leftJoin("e.category", "category");
            $qb->andWhere("category.slug = :category")->setParameter("category", $category);
        }
        if ($venue !== "all" || $country !== "all" || $location !== "all" || $pricemin !== "all" || $pricemax !== "all" || $startdate != "all" || $startdatemin != "all" || $startdatemax != "all" || $sort === "eventdates.startdate" || $elapsed !== "all" || $onsalebypos !== "all" || $onlineonly !== "all") {
            $qb->leftJoin("e.eventdates", "eventdates");
            $qb->leftJoin("eventdates.venue", "venue");
        }
        if ($onlineonly == "1") {
            $qb->andWhere("eventdates.online = 1");
        }
        if ($freeonly == "1" || $pricemin !== "all" || $pricemax !== "all") {
            $qb->leftJoin("eventdates.tickets", "tickets");
        }
        if ($freeonly == "1") {
            $qb->andWhere("tickets.free = 1");
        } elseif ($pricemin !== "all" || $pricemax !== "all") {
            if ($pricemin !== "all") {
                $qb->andWhere("(tickets.price >= :pricemin AND tickets.promotionalprice IS NULL) OR (tickets.promotionalprice >= :pricemin)")->setParameter("pricemin", $pricemin);
            }
            if ($pricemax !== "all") {
                $qb->andWhere("((tickets.price <= :pricemax OR tickets.price IS NULL) AND tickets.promotionalprice IS NULL) OR (tickets.promotionalprice <= :pricemax)")->setParameter("pricemax", $pricemax);
            }
        }
        if ($startdate !== "all") {
            $qb->andWhere("Date(eventdates.startdate) = :startdate")->setParameter("startdate", $startdate);
        }
        if ($startdatemin !== "all") {
            $qb->andWhere("Date(eventdates.startdate) >= :startdatemin")->setParameter("startdatemin", $startdatemin);
        }
        if ($startdatemax !== "all") {
            $qb->andWhere("Date(eventdates.startdate) <= :startdatemax")->setParameter("startdatemax", $startdatemax);
        }
        if ($organizer !== "all" || $organizerEnabled !== "all") {
            $qb->leftJoin("e.organizer", "organizer");
        }
        if ($organizer !== "all") {
            $qb->andWhere("organizer.slug = :organizer")->setParameter("organizer", $organizer);
        }
        if ($keyword !== "all") {
            $qb->andWhere("e.name LIKE :keyword or :keyword LIKE e.name or e.description LIKE :keyword or :keyword LIKE e.description or e.tags LIKE :keyword or :keyword LIKE e.tags or e.artists LIKE :keyword or :keyword LIKE e.artists")->setParameter("keyword", "%" . $keyword . "%");
        }
        if ($slug !== "all") {
            $qb->andWhere("e.slug = :slug")->setParameter("slug", $slug);
        }
        if ($addedtofavoritesby !== "all") {
            $qb->andWhere(":addedtofavoritesbyuser MEMBER OF e.addedtofavoritesby")->setParameter("addedtofavoritesbyuser", $addedtofavoritesby);
        }
        if ($isOnHomepageSlider === true) {
            $qb->andWhere("e.isonhomepageslider IS NOT NULL");
        }
        if ($published !== "all") {
            $qb->andWhere("e.published = :published")->setParameter("published", $published);
        }
        if ($otherthan !== "all") {
            $qb->andWhere("e.slug != :otherthan")->setParameter("otherthan", $otherthan);
            $qb->andWhere("e.slug = :otherthan")->setParameter("otherthan", $otherthan);
        }
        if ($elapsed !== "all") {
            if ($elapsed === true || $elapsed == "1") {
                $qb->andWhere("eventdates.startdate < CURRENT_TIMESTAMP()");
            } else if ($elapsed === false || $elapsed == "0") {
                $qb->andWhere("eventdates.startdate >= CURRENT_TIMESTAMP()");
            }
        }
        if ($organizerEnabled !== "all") {
            $qb->leftJoin("organizer.user", "user");
            $qb->andWhere("user.enabled = :userEnabled")->setParameter("userEnabled", $organizerEnabled);
        }
        $qb->orderBy('eventdates.startdate', 'DESC');

        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }
        return $qb;
    }

    public function getEventsCreatedByUserAndSortByUpdatedAt($organizerId)
    {
        $qb = $this->createQueryBuilder("e")
            ->join("e.organizer", "organizer")
            ->andWhere("organizer.id = :organizerId")
            ->setParameter("organizerId", $organizerId)
            ->orderBy("e.updatedAt", "DESC");
        return $qb->getQuery()->getResult();
    }

    public function getAllEvents(): QueryBuilder
    {
        $qb = $this->createQueryBuilder("e");
        $qb->select("DISTINCT e");
        return $qb;
    }

    public function findEventsByCommonValue($commonValue)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.commonvalue = :commonValue')
            ->setParameter('commonValue', $commonValue)
            ->getQuery()
            ->getResult();
    }

    public function getEventsByOrganizerId($organizerId): QueryBuilder
    {
        return $this->createQueryBuilder("e")
            ->join("e.organizer", "organizer")
            ->andWhere("organizer.id = :organizerId")
            ->setParameter("organizerId", $organizerId);
    }

    public function getAllEventsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder("e")
            ->select("DISTINCT e");
    }

    public function findOneBySlug($slug)
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}