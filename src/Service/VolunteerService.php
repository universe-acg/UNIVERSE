<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\VolunteerEvent;
use Doctrine\ORM\EntityManagerInterface;

class VolunteerService
{
    private $entityManager;
    private $userService;

    public function __construct(EntityManagerInterface $entityManager, UserService $userService)
    {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
    }

    // Example: Create a new volunteer event
    public function createVolunteerEvent($userid, $event, $message)
    {
        $volunteerEvent = new VolunteerEvent();
        $volunteerEvent->setUser($userid);
        $volunteerEvent->setEventid($event->getId());
        $volunteerEvent->setMessage($message);

        $this->entityManager->persist($volunteerEvent);
        $this->entityManager->flush();
    }

    // Example: Update a volunteer event
    public function updateVolunteerEvent($id, $userid, $event, $message)
    {
        $volunteerEvent = $this->entityManager->getRepository(VolunteerEvent::class)->find($id);

        if ($volunteerEvent) {
            $volunteerEvent->setUserid($userid);
            $volunteerEvent->setEventid($event);
            $volunteerEvent->setMessage($message);

            $this->entityManager->flush();
        }
    }

    // Example: Delete a volunteer event
    public function deleteVolunteerEvent($id)
    {
        $volunteerEvent = $this->entityManager->getRepository(VolunteerEvent::class)->find($id);

        if ($volunteerEvent) {
            $this->entityManager->remove($volunteerEvent);
            $this->entityManager->flush();
        }
    }

    public function findVolunteerEventById($id)
    {
        // Use the repository to find a volunteer event by its ID
        return $this->entityManager->getRepository(VolunteerEvent::class)->find($id);
    }

    public function findVolunteersByEventIdAndUserId($eventid)
    {
        return $this->entityManager->getRepository(VolunteerEvent::class)->findVolunteersByEventIdAndUserId($eventid);
    }

    // Example: Get a list of volunteer events for a specific user
    public function getVolunteerEventsByUserId($userid)
    {
        return $this->entityManager->getRepository(VolunteerEvent::class)->findVolunteerEventsByUserId($userid);
    }

    // Example: Get a list of volunteer events by event name
    public function getVolunteerEventsByEventName($eventName)
    {
        return $this->entityManager->getRepository(VolunteerEvent::class)->findVolunteerEventsByEventName($eventName);
    }

    // Example: Delete all volunteer events for a specific user
    public function deleteVolunteerEventsByUserId($userid)
    {
        $volunteerEvents = $this->entityManager->getRepository(VolunteerEvent::class)->findVolunteerEventsByUserId($userid);

        foreach ($volunteerEvents as $event) {
            $this->entityManager->remove($event);
        }

        $this->entityManager->flush();
    }

    // Add more custom methods as needed
}