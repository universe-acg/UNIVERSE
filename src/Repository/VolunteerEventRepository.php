<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\VolunteerEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VolunteerEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VolunteerEvent::class);
    }

    // Example: Find a volunteer event by ID
    public function findVolunteerEventById(int $id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    // Example: Find all volunteer events
    public function findAllVolunteerEvents()
    {
        return $this->findAll();
    }

    /**
     * Find a volunteer event by ID and user ID.
     *
     * @param int $eventId
     * @param int $userId
     *
     * @return VolunteerEvent|null
     */
    public function findVolunteerEventByIdAndUserId(int $eventId, int $userId)
    {
        return $this->createQueryBuilder('ve')
            ->where('ve.event = :eventId')
            ->andWhere('ve.user = :userId')
            ->setParameters(['eventId' => $eventId, 'userId' => $userId])
            ->getQuery()
            ->getResult();
    }

    // Example: Find volunteer events by user ID
    public function findVolunteerEventsByUserId(int $userid)
    {
        return $this->findBy(['userId' => $userid]);
    }

    /**
     * Find volunteers by event ID and check if the organizer's user ID matches the provided user ID.
     *
     * @param int $eventId
     * @param int $userId
     *
     * @return VolunteerEvent[]
     */
    public function findVolunteersByEventIdAndUserId(int $eventId)
    {
        return $this->createQueryBuilder('ve')
            ->where('ve.eventId = :eventId')
//            ->andWhere('ve.user = :userId')
//            ->setParameters(['eventId' => $eventId, 'userId' => $userId])
            ->setParameters(['eventId' => $eventId])
            ->getQuery()
            ->getResult();
    }

    // Example: Find volunteer events by event name
    public function findVolunteerEventsByEventName(string $eventName)
    {
        return $this->findBy(['event' => $eventName]);
    }
    // Add more custom methods as needed
}