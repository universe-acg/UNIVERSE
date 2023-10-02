<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\EventSubscriber;
    
    use App\Service\AppServices;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
    use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
    
    class CustomLoginListener implements EventSubscriberInterface
    {
        
        private AppServices $appServices;
        
        /**
         * @param AppServices $appServices
         */
        public function __construct(AppServices $appServices)
        {
            $this->appServices = $appServices;
        }
        
        public static function getSubscribedEvents()
        {
            return [
                'security.interactive_login' => 'onSecurityInteractiveLogin',
            ];
        }
        
        public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
        {
            $authenticationToken = $event->getAuthenticationToken();
            if ($authenticationToken instanceof UsernamePasswordToken) {
                // Get the user from the authentication token
                $user = $authenticationToken->getUser();
                if ($user->getRole() == "Organizer") {
                    $this->appServices->updateCreatorBadge($user);
                } elseif ($user->getRole() == "Attendee") {
                    $this->appServices->updateAttendeeBadge($user);
                }
            }
        }
    }