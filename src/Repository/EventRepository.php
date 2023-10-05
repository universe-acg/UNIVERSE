<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\Event;
    use App\Entity\Organizer;
    use App\Entity\User;
    use DateTime;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\ORM\QueryBuilder;
    use Doctrine\Persistence\ManagerRegistry;
    
    class EventRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Event::class);
        }
        
        public function findEventsStartingOn(DateTime $date)
        {
            return $this->createQueryBuilder('e')
                ->leftJoin('e.eventdates', 'eventdates')
                ->where('eventdates.startdate >= :dateStart')
                ->andWhere('eventdates.startdate < :dateEnd')
                ->setParameter('dateStart', $date->format('Y-m-d 00:00:00'))
                ->setParameter('dateEnd', $date->format('Y-m-d 23:59:59'))
                ->getQuery()
                ->getResult();
        }
        
        public function findAttendedEventsByUser(User $user, $category, $startdate)
        {
            $startDateFilter = $startdate ?? "all";
            
            // Add date range filtering
            $startDateMin = null;
            $startDateMax = null;
            
            if ($startDateFilter === "today") {
                $startDateMin = new DateTime('today');
                $startDateMax = new DateTime('today');
            } elseif ($startDateFilter === "tomorrow") {
                $startDateMin = new DateTime('tomorrow');
                $startDateMax = new DateTime('tomorrow');
            } elseif ($startDateFilter === "thisweekend") {
                $startDateMin = new DateTime('saturday this week');
                $startDateMax = new DateTime('sunday this week');
            } elseif ($startDateFilter === "thisweek") {
                $startDateMin = new DateTime('monday this week');
                $startDateMax = new DateTime('sunday this week');
            } elseif ($startDateFilter === "nextweek") {
                $startDateMin = new DateTime('monday next week');
                $startDateMax = new DateTime('sunday next week');
            } elseif ($startDateFilter === "thismonth") {
                $startDateMin = new DateTime('first day of this month');
                $startDateMax = new DateTime('last day of this month');
            } elseif ($startDateFilter === "nextmonth") {
                $startDateMin = new DateTime('first day of next month');
                $startDateMax = new DateTime('last day of next month');
            }
            
            $qb = $this->createQueryBuilder('e');
            $qb->join('e.eventdates', 'ed') // Assuming events are related to event dates
            ->join('ed.tickets', 'et') // Assuming event dates are related to event tickets
            ->join('et.orderelements', 'oe') // Assuming event tickets are related to order elements
            ->join('oe.order', 'o') // Assuming order elements are related to orders
            ->where('o.user = :user');
            
            if ($startDateMin && $startDateMax) {
                $qb
                    ->andWhere('ed.startdate >= :startDateMin')
                    ->andWhere('ed.startdate <= :startDateMax')
                    ->setParameter('startDateMin', $startDateMin->format('Y-m-d H:i:s'))
                    ->setParameter('startDateMax', $startDateMax->format('Y-m-d H:i:s'));
            }
            
            $qb->andWhere('o.status = :status')
                ->leftJoin("e.category", "category")
                ->andWhere("category.slug like :category")->setParameter("category", $category != 'all' ? $category : '%')
                ->setParameter('user', $user)
                ->setParameter('status', 2);
            
            return $qb;
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
        
        public function findUpcomingEvents(): array
        {
            $now = new DateTime();
            
            return $this->createQueryBuilder('e')
                ->leftJoin('e.eventdates', 'eventdates')
                ->where('e.published = :published')
                ->andWhere('eventdates.startdate > :now')
                ->setParameter('published', true)
                ->setParameter('now', $now)
                ->orderBy('eventdates.startdate', 'ASC')
                ->setMaxResults(8)
                ->getQuery()
                ->getResult();
        }
        
        public function findById($id)
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        public function getEvents($category, $organizer, $keyword, $slug, $onlineonly, $startdate, $startdatemin, $startdatemax, $published, $elapsed, $organizerEnabled, $addedtofavoritesby, $isOnHomepageSlider, $otherthan, $limit, $count): QueryBuilder
        {
            $qb = $this->createQueryBuilder("e");
            if ($count) {
                $qb->select("COUNT(e)");
            } else {
                $qb->select("DISTINCT e");
            }
            
            $qb->leftJoin('e.eventdates', 'eventdates');
            
            if ($category !== "all") {
                $qb->leftJoin("e.category", "category");
                $qb->andWhere("category.slug = :category")->setParameter("category", $category);
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
                $keyword = "%" . $keyword . "%";
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('e.name', ':keyword'),
                        $qb->expr()->like('e.description', ':keyword'),
                        $qb->expr()->like('e.tags', ':keyword'),
                    )
                )->setParameter('keyword', $keyword);
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
        
        public function findOneBySlug($slug)
        {
            return $this->findOneBy(['slug' => $slug]);
        }
        
        
        public function findTotalEventsAttendedByUser(User $user)
        {
            $qb = $this->createQueryBuilder('e')
                ->join('e.eventdates', 'ed')
                ->join('ed.tickets', 'et')
                ->join('et.orderelements', 'oe')
                ->join('oe.order', 'o')
                ->where('o.user = :user')
                ->andWhere('o.status = :status')
                ->setParameter('user', $user)
                ->setParameter('status', 2);
            
            return $qb->getQuery()->getResult();
        }
        
        public function findEventsCreatedByOrganizer(Organizer $organizer)
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.organizer = :organizer')
                ->setParameter('organizer', $organizer)
                ->getQuery()
                ->getResult();
        }
    }