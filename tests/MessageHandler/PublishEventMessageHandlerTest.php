<?php
namespace App\Tests\MessageHandler;
use App\Entity\Event;
use App\Message\PublishEventMessage;
use App\MessageHandler\PublishEventMessageHandler;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
final class PublishEventMessageHandlerTest extends TestCase
{
    public function testCanceledScheduleCannotPublish(): void {$this->check(false,new \DateTime('-1 hour'),false);}
    public function testOldMessageCannotPublishRescheduledEventEarly(): void {$this->check(true,new \DateTime('+1 hour'),false);}
    public function testDueSchedulePublishes(): void {$this->check(true,new \DateTime('-1 hour'),true);}
    private function check(bool $scheduled,\DateTime $date,bool $expected): void {
        $event=new Event();$event->setPublished(false);$event->setPublishOnSpecificDate($scheduled);$event->setPublishDate($date);
        $repository=$this->createMock(EventRepository::class);$repository->method('find')->willReturn($event);
        $em=$this->createMock(EntityManagerInterface::class);$em->expects($expected?$this->once():$this->never())->method('flush');
        (new PublishEventMessageHandler($repository,$em))(new PublishEventMessage(123));
        self::assertSame($expected,$event->getPublished());
    }
}
