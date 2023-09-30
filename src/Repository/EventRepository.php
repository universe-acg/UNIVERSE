<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\Event;
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
        
        public function findAttendedEventsByUser(User $user)
        {
            return $this->createQueryBuilder('e')
                ->join('e.eventdates', 'ed') // Assuming events are related to event dates
                ->join('ed.tickets', 'et') // Assuming event dates are related to event tickets
                ->join('et.orderelements', 'oe') // Assuming event tickets are related to order elements
                ->join('oe.order', 'o') // Assuming order elements are related to orders
                ->where('o.user = :user')
                ->andWhere('o.status = :status')
                ->setParameter('user', $user)
                ->setParameter('status', 2);
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
    }