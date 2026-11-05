<?php
namespace App\Service;
use App\Entity\{AthleticBooking,FacilityEntity,MonthlySchedule,OperatingHours,User};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\LockMode;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\{AccessDeniedHttpException,NotFoundHttpException};
final class AthleticBookingService
{
    public function __construct(private EntityManagerInterface $em,private Security $security) {}
    public function getAthleticBookingById($id): ?AthleticBooking {return $this->em->find(AthleticBooking::class,$id);}
    public function getAthleticBookingByFacility($facility): array {return $this->em->getRepository(AthleticBooking::class)->findBy(['facility'=>$facility],['bookingDate'=>'DESC']);}
    public function getAthleticBookings(): array {return $this->em->getRepository(AthleticBooking::class)->findAll();}
    public function deleteAthleticBooking($id): void {
        $booking=$this->getAthleticBookingById($id);if(!$booking)throw new NotFoundHttpException();$this->owner($booking);$this->em->remove($booking);$this->em->flush();
    }
    public function cancelAthleticBooking($id): void {$this->deleteAthleticBooking($id);}
    private function owner(AthleticBooking $booking): void {
        if(!$this->security->isGranted('ROLE_ADMINISTRATOR')&&$booking->getUser()?->getId()!==$this->security->getUser()?->getId())throw new AccessDeniedHttpException();
    }
    public function createAthleticBooking($facilityId,$userId,$scheduleId,$bookingDate,$duration): AthleticBooking {
        $facility=$this->em->find(FacilityEntity::class,$facilityId);$user=$this->em->find(User::class,$userId);$hours=$this->em->find(OperatingHours::class,$scheduleId);
        if(!$facility||!$user||!$hours||$hours->getMonthlySchedule()?->getFacility()?->getId()!==$facility->getId())throw new NotFoundHttpException();
        $date=\DateTime::createFromFormat('!Y-m-d',(string)$bookingDate);if(!$date)throw new \InvalidArgumentException('Invalid booking date.');$date->setTime((int)$hours->getStartTime()->format('H'),(int)$hours->getStartTime()->format('i'));
        $booking=new AthleticBooking();$booking->setFacility($facility);$booking->setUser($user);$booking->setBookingDate($date);$booking->setDuration((int)$duration);$booking->setAmount(max(1,(int)$facility->getMinPlayers()));$booking->setStatus(false);$this->reserve($booking);return $booking;
    }
    public function reserve(AthleticBooking $booking): void {
        $user=$this->security->getUser();if(!$user||$booking->getUser()?->getId()!==$user->getId())throw new AccessDeniedHttpException();
        $facility=$booking->getFacility();$start=$booking->getBookingDate();$duration=(int)$booking->getDuration();
        if(!$facility||$facility->isHidden()||!$facility->getPublished()||!$start||$start<=new \DateTime())throw new \InvalidArgumentException('Select an available future reservation.');
        if(!in_array($duration,[30,60,90,120],true)||$booking->getAmount()<$facility->getMinPlayers()||$booking->getAmount()>$facility->getMaxPlayers())throw new \InvalidArgumentException('Select a valid duration and number of players.');
        $end=(clone $start)->modify('+'.$duration.' minutes');$open=false;
        foreach($this->em->getRepository(MonthlySchedule::class)->findBy(['facility'=>$facility]) as $schedule){
            if($start->format('Y-m-d')<$schedule->getStartDate()->format('Y-m-d')||$start->format('Y-m-d')>$schedule->getEndDate()->format('Y-m-d'))continue;
            foreach($schedule->getOperatingHours() as $hours){$day=strtolower($hours->getDayOfWeek());if(!in_array($day,[strtolower($start->format('l')),$start->format('N')],true))continue;if($start->format('H:i')>=$hours->getStartTime()->format('H:i')&&$end->format('Y-m-d')===$start->format('Y-m-d')&&$end->format('H:i')<=$hours->getEndTime()->format('H:i'))$open=true;}
        }
        if(!$open)throw new \InvalidArgumentException('The facility is closed for the selected time.');
        $this->em->wrapInTransaction(function()use($booking,$facility,$start,$end){
            $this->em->lock($facility,LockMode::PESSIMISTIC_WRITE);
            $others=$this->em->getRepository(AthleticBooking::class)->createQueryBuilder('b')->where('b.facility = :facility')->andWhere('b.bookingDate < :end')->andWhere('b.bookingDate >= :day')->setParameter('facility',$facility)->setParameter('end',$end)->setParameter('day',(clone $start)->setTime(0,0))->getQuery()->getResult();
            foreach($others as $other)if($other!==$booking&&(clone $other->getBookingDate())->modify('+'.$other->getDuration().' minutes')>$start)throw new \InvalidArgumentException('This time has already been reserved. Select another time.');
            $this->em->persist($booking);$this->em->flush();
        });
    }
}
