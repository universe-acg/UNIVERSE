<?php
namespace App\EventSubscriber;
use App\Entity as E;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
final class EntityAccessSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em,private Security $security){}
    public static function getSubscribedEvents(): array {return ['kernel.request'=>['guard',1]];}
    public function guard(RequestEvent $event): void {
        $r=$event->getRequest();$controller=$r->attributes->get('_controller');if(!is_string($controller)||!str_contains($controller,'::'))return;
        [$class,$method]=explode('::',$controller,2);$user=$this->security->getUser();$admin=$this->security->isGranted('ROLE_ADMINISTRATOR');if($admin)return;
        $shared='App\\Controller\\Dashboard\\Shared\\';$slug=$r->attributes->get('slug');$entity=null;$owner=null;
        $types=['EventController'=>E\Event::class,'ListingController'=>E\Listing::class,'BlogPostController'=>E\BlogPost::class,'ReelsController'=>E\Reel::class];
        foreach($types as $name=>$type)if($class===$shared.$name&&$slug&&$method!=='apply'){
            $entity=$this->em->getRepository($type)->findOneBy(['slug'=>$slug]);$owner=$entity&&method_exists($entity,'getOrganizer')?$entity->getOrganizer()?->getUser():$entity?->getUser();
            if($entity&&(!$owner||!$user||$owner->getId()!==$user->getId()))throw new AccessDeniedHttpException();
        }
        if($class===$shared.'VenueController')throw new AccessDeniedHttpException();
        if($class===$shared.'PdfController'&&($slug=$r->attributes->get('event'))){$record=$this->em->getRepository(E\Event::class)->findOneBy(['slug'=>$slug]);if(!$record||!$user||$record->getOrganizer()?->getUser()?->getId()!==$user->getId())throw new AccessDeniedHttpException();}
        if($class===$shared.'OrderController'&&($reference=$r->attributes->get('reference'))){
            $order=$this->em->getRepository(E\Order::class)->findOneBy(['reference'=>$reference]);
            if($order&&$order->getUser()?->getId()!==$user?->getId()){
                $allowed=$this->security->isGranted('ROLE_ORGANIZER')&&count($order->getOrderelements())>0;
                foreach($order->getOrderelements() as $element)if($element->getEventticket()?->getEventdate()?->getEvent()?->getOrganizer()?->getUser()?->getId()!==$user?->getId())$allowed=false;
                if(!$allowed)throw new AccessDeniedHttpException();
            }
        }
        if($class==='App\\Controller\\Api\\UserController'&&$method==='getMembers'&&!$this->security->isGranted('ROLE_ORGANIZER'))throw new AccessDeniedHttpException();
    }
}
