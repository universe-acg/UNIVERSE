<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;


use App\Entity\Event;
use App\Entity\HelpCenterCategory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EventService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $criterias
     * @return array
     * @throws Exception
     */
    public function getEvents($criterias)
    {
        // Extract criteria variables
        $category = $criterias['category'] ?? "all";
        $organizer = $criterias['organizer'] ?? "all";
        $keyword = $criterias['keyword'] ?? "all";
        $open = $criterias['open'] ?? "all";
        $onlineonly = $criterias['onlineonly'] ?? false;
        $startDateFilter = $criterias['startdate'] ?? "all";
        $slug = $criterias['slug'] ?? "all";
        $otherthan = $criterias['otherthan'] ?? "all";
        $published = $criterias['published'] ?? "all";
        $addedtofavoritesby = $criterias['addedtofavoritesby'] ?? "all";
        $sort = $criterias['sort'] ?? "eventdates.startdate";
        $order = $criterias['order'] ?? "ASC";
        $limit = $criterias['limit'] ?? "all";
        $count = $criterias['count'] ?? false;
        $elapsed = $criterias['elapsed'] ?? "all";

        // Create the query builder
        $queryBuilder = $this->em->getRepository(Event::class)->createQueryBuilder('e');

        // Add conditions based on criteria
        if ($category !== "all") {
            $queryBuilder
                ->andWhere('e.category = :category')
                ->setParameter('category', $category);
        }

        if ($addedtofavoritesby !== "all") {
            $queryBuilder->andWhere(":addedtofavoritesbyuser MEMBER OF e.addedtofavoritesby")->setParameter("addedtofavoritesbyuser", $addedtofavoritesby);
        }

        if ($slug !== "all") {
            // Add a condition for the slug
            $queryBuilder
                ->andWhere('e.slug = :slug')
                ->setParameter('slug', $slug);
        }
        if ($organizer !== "all") {
            $queryBuilder->leftJoin("e.organizer", "organizer");
            $queryBuilder->andWhere("organizer.slug = :organizer")
                ->setParameter("organizer", $organizer);
        }

        if ($published !== "all") {
            $queryBuilder->andWhere('e.published = :published')->setParameter('published', $published);
        }

        if ($open !== "all") {
            $queryBuilder->andWhere('e.open = :open')->setParameter('open', $open);
        }


        if ($category !== "all") {
            // Add a condition for the category
            $queryBuilder
                ->andWhere('e.category = :category')
                ->setParameter('category', $category);
        }
        if ($keyword !== "all") {
            // Add a condition for the keyword
            $queryBuilder
                ->andWhere('e.name LIKE :keyword OR e.description LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
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
        } elseif ($startDateFilter === "pickadate") {
            // Handle custom date selection logic here if needed
            $customDate = $criterias['custom_date'] ?? null;
            if ($customDate) {
                $startDateMin = new DateTime($customDate);
                $startDateMax = new DateTime($customDate);
            }
        }

        if ($otherthan !== "all") {
            $queryBuilder->andWhere("e.slug != :otherthan")->setParameter("otherthan", $otherthan);
        }

        $queryBuilder
            ->leftJoin('e.eventdates', 'eventdates');


        if ($startDateMin && $startDateMax) {
            $queryBuilder
                ->andWhere('eventdates.startdate >= :startDateMin')
                ->andWhere('eventdates.startdate <= :startDateMax')
                ->setParameter('startDateMin', $startDateMin)
                ->setParameter('startDateMax', $startDateMax);
        }
        if ($elapsed !== "all") {
            if ($elapsed === true || $elapsed == "1") {
                $queryBuilder->andWhere("eventdates.startdate < CURRENT_TIMESTAMP()");
            } else if ($elapsed === false || $elapsed == "0") {
                $queryBuilder->andWhere("eventdates.startdate >= CURRENT_TIMESTAMP()");
            }
        }

        $events = $queryBuilder->getQuery()->getResult();

        $eventsToRemove = [];
        $eventsByCommonValue = [];

        // Step 1: Iterate through each event
        foreach ($events as $event) {
            if ($event->isIsrepeat()) {
                $commonValue = $event->getCommonvalue();


                // Step 2: Create an array of events with the same common value
                if (!isset($eventsByCommonValue[$commonValue])) {
                    $eventsByCommonValue[$commonValue] = [];
                }
                $eventsByCommonValue[$commonValue][] = $event;
            }
        }

        // Step 3: Filter and sort events for each common value
        foreach ($eventsByCommonValue as $commonValue => $commonEvents) {
            $filteredEvents = array_filter($commonEvents, static function ($event) {
                return $event->getLatestEndDate() >= new DateTime();
            });

            // Sort the events based on the latest event date
            usort($filteredEvents, static function ($eventA, $eventB) {
                $latestEventDateA = $eventA->getLatestEndDate();
                $latestEventDateB = $eventB->getLatestEndDate();

                if ($latestEventDateA === null && $latestEventDateB === null) {
                    return 0;
                }

                if ($latestEventDateA === null) {
                    return 1;
                }

                if ($latestEventDateB === null) {
                    return -1;
                }

                return $latestEventDateA <=> $latestEventDateB;
            });

            // Step 4: Add the first event to the removal list
            if (count($filteredEvents) > 1) {
                $eventsToRemove = array_merge($eventsToRemove, array_slice($filteredEvents, 1));
            }
        }

        // Step 5: Remove events with the same common value
        $events = array_filter($events, static function ($event) use ($eventsToRemove) {
            return !in_array($event, $eventsToRemove, true);
        });

        $eventsToRemove = [];
        $eventsByCommonValue = [];

        // Step 1: Iterate through each event
        foreach ($events as $event) {
            if ($event->isIsrepeat()) {
                $commonValue = $event->getCommonvalue();


                // Step 2: Create an array of events with the same common value
                if (!isset($eventsByCommonValue[$commonValue])) {
                    $eventsByCommonValue[$commonValue] = [];
                }
                $eventsByCommonValue[$commonValue][] = $event;
            }
        }

        // Step 3: Filter and sort events for each common value
        foreach ($eventsByCommonValue as $commonValue => $commonEvents) {
            $filteredEvents = array_filter($commonEvents, static function ($event) {
                return $event->getLatestEndDate() >= new DateTime();
            });

            // Sort the events based on the latest event date
            usort($filteredEvents, static function ($eventA, $eventB) {
                $latestEventDateA = $eventA->getLatestEndDate();
                $latestEventDateB = $eventB->getLatestEndDate();

                if ($latestEventDateA === null && $latestEventDateB === null) {
                    return 0;
                }

                if ($latestEventDateA === null) {
                    return 1;
                }

                if ($latestEventDateB === null) {
                    return -1;
                }

                return $latestEventDateA <=> $latestEventDateB;
            });

            // Step 4: Add the first event to the removal list
            if (count($filteredEvents) > 1) {
                $eventsToRemove = array_merge($eventsToRemove, array_slice($filteredEvents, 1));
            }
        }

        // Step 5: Remove events with the same common value
        $events = array_filter($events, static function ($event) use ($eventsToRemove) {
            return !in_array($event, $eventsToRemove, true);
        });

        // Execute the query
        return $events;
    }

    public function getHelpCenterCategories($criterias)
    {
        $parent = array_key_exists('parent', $criterias) ? $criterias['parent'] : "all";
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "ASC";

        $order = "articlescount";

        return $this->em->getRepository(HelpCenterCategory::class)->getHelpCenterCategories($parent, $hidden, $keyword, $slug, $limit, $order, $sort);
    }

    public function getEventBySlug(string $slug)
    {
        // Implement the logic to fetch an event by slug
        return $this->em->getRepository(Event::class)->findOneBy(['slug' => $slug]);
    }

    public function getEventById(int $id)
    {
        // Implement the logic to fetch an event by ID
        return $this->em->getRepository(Event::class)->find($id);
    }
}