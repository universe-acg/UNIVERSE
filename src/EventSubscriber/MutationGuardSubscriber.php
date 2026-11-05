<?php
namespace App\EventSubscriber;
use App\Entity as E;
use App\Security\MutationRoutes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\{CsrfToken,CsrfTokenManagerInterface};
final class MutationGuardSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security,private CsrfTokenManagerInterface $csrf,private EntityManagerInterface $em) {}
    public static function getSubscribedEvents(): array { return ['kernel.request'=>['guard',0]]; }
    public function guard(RequestEvent $event): void {
        $request=$event->getRequest();$controller=$request->attributes->get('_controller');if(!is_string($controller))return;
        if(str_contains($controller,'\\Dashboard\\Administrator\\')&&!$this->security->isGranted('ROLE_ADMINISTRATOR'))throw new AccessDeniedHttpException();
        if(str_contains($controller,'\\Dashboard\\Shared\\AthleticBookingController::') && in_array(explode('::',$controller)[1],['manage','addedit','delete','restore','showhide'],true) && !$this->security->isGranted('ROLE_ADMINISTRATOR'))throw new AccessDeniedHttpException();
        if(!MutationRoutes::isMutation($controller))return;
        $user=$this->security->getUser();if(!$user)throw new AccessDeniedHttpException();
        $token=$request->headers->get('X-CSRF-Token')??$request->request->get('_action_token')??$request->request->get('_token');
        if(!$request->isMethod('POST')||!is_string($token)||!$this->csrf->isTokenValid(new CsrfToken('action',$token)))throw new AccessDeniedHttpException('Invalid action token.');
        if($this->security->isGranted('ROLE_ADMINISTRATOR'))return;
        [$class,$method]=explode('::',$controller,2);$slug=$request->attributes->get('slug');$owner=null;
        if($class==='App\\Controller\\Dashboard\\Shared\\EventController'&&$slug){$record=$this->em->getRepository(E\Event::class)->findOneBy(['slug'=>$slug]);$owner=$record?->getOrganizer()?->getUser();}
        if($class==='App\\Controller\\Dashboard\\Shared\\ListingController'&&$slug&&$method!=='apply'){$record=$this->em->getRepository(E\Listing::class)->findOneBy(['slug'=>$slug]);$owner=$record?->getOrganizer()?->getUser();}
        if($class==='App\\Controller\\Dashboard\\Shared\\VenueController')throw new AccessDeniedHttpException();
        if($class==='App\\Controller\\Dashboard\\Shared\\AthleticBookingController'&&($id=$request->attributes->get('athleticBookingId'))){$record=$this->em->find(E\AthleticBooking::class,$id);$owner=$record?->getUser();}
        if($class==='App\\Controller\\Dashboard\\Shared\\OrderController'&&!$this->security->isGranted('ROLE_ORGANIZER')&&($reference=$request->attributes->get('reference'))){$record=$this->em->getRepository(E\Order::class)->findOneBy(['reference'=>$reference]);$owner=$record?->getUser();}
        if(isset($record)&&(!$owner||$owner->getId()!==$user->getId()))throw new AccessDeniedHttpException();
        if(str_contains($controller,'NotificationController::')&&($id=$request->attributes->get('id'))){$notification=$this->em->find(E\Notification::class,$id);if(!$notification||$notification->getUserid()!==$user->getId())throw new AccessDeniedHttpException();}
    }
}
