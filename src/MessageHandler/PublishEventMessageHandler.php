<?php
namespace App\MessageHandler;
use App\Message\PublishEventMessage;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
#[AsMessageHandler]
final class PublishEventMessageHandler
{
    public function __construct(private EventRepository $events,private EntityManagerInterface $em) {}
    public function __invoke(PublishEventMessage $message): void {
        $event=$this->events->find($message->getEventId());
        // Ignore canceled or superseded messages after an organizer changes the schedule.
        if(!$event || !$event->isPublishOnSpecificDate() || !$event->getPublishDate() || $event->getPublishDate()>new \DateTimeImmutable())return;
        if(!$event->getPublished()){$event->setPublished(true);$this->em->persist($event);$this->em->flush();}
    }
}
