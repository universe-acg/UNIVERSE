<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\MessageHandler;

use App\Message\PublishEventMessage;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PublishEventMessageHandler implements MessageHandlerInterface
{
    private $eventRepository;
    private $entityManager;
    private $logger;

    public function __construct(EventRepository $eventRepository, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function __invoke(PublishEventMessage $message)
    {

        $eventId = $message->getEventId();

        $event = $this->eventRepository->find($eventId);

        if ($event) {
            // Update the event's publication status
            $event->setPublished(true);

            try {
                $this->entityManager->persist($event);
                $this->entityManager->flush();
            } catch (Exception $e) {
                // Handle exceptions (e.g., log and/or throw)
                $this->logger->error('Error updating event publication status: ' . $e->getMessage());
            }
        } else {
            $this->logger->warning('Event not found for ID: ' . $eventId);
        }
    }
}
