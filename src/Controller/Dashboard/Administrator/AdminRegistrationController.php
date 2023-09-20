<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Dashboard\Administrator;
    
    use App\Form\AdminRegistrationOrganizerType;
    use App\Form\AdminRegistrationTypeAttendee;
    use App\Service\AppServices;
    use FOS\UserBundle\Event\FormEvent;
    use FOS\UserBundle\Event\GetResponseUserEvent;
    use FOS\UserBundle\FOSUserEvents;
    use FOS\UserBundle\Model\UserManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    
    class AdminRegistrationController extends Controller
    {
        
        private $eventDispatcher;
        private $formFactory;
        private $userManager;
        private $tokenStorage;
        private $services;
        
        public function __construct(EventDispatcherInterface $eventDispatcher, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage, AppServices $services)
        {
            $this->eventDispatcher = $eventDispatcher;
            $this->userManager = $userManager;
            $this->tokenStorage = $tokenStorage;
            $this->services = $services;
        }
        
        /**
         * @Route("/administrator/signup/attendee", name="registration_register_attendee_admin")
         */
        public function registerAttendeeAction(Request $request): Response
        {
            $user = $this->userManager->createUser();
            
            $form = $this->createForm(AdminRegistrationTypeAttendee::class, $user);
            
            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
            
            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
            
            $form->setData($user);
            
            $form->handleRequest($request);
            
            
            if ($form->isSubmitted() && $form->isValid()) {
                $event = new FormEvent($form, $request);
                if ($form->isValid()) {
                    $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                    $user->addRole('ROLE_ATTENDEE');
                    $user->setEnabled(true);
                    
                    $this->userManager->updateUser($user);
                    $this->addFlash('success', 'Attendee created');
                    return $this->redirectToRoute("dashboard_index");
                }
                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);
                $this->addFlash('error', 'Error. Contact admin.');
                if (null !== $response = $event->getResponse()) {
                    return $response;
                }
            }
            
            return $this->render('Dashboard/Administrator/User/Registration/admin-register-attendee.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        
        /**
         * @Route("/administrator/signup/organizer", name="registration_register_organizer_admin")
         */
        public function registerOrganizerAction(Request $request): Response
        {
            $user = $this->userManager->createUser();
            
            $form = $this->createForm(AdminRegistrationOrganizerType::class, $user);
            
            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
            
            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
            
            $form->setData($user);
            
            $form->handleRequest($request);
            
            
            if ($form->isSubmitted() && $form->isValid()) {
                $event = new FormEvent($form, $request);
                if ($form->isValid()) {
                    $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                    $user->addRole('ROLE_ORGANIZER');
                    $user->setEnabled(true);
                    $creatorCategory = $form->get('organizer')->get('category')->getData();
                    $user->getOrganizer()->setCategory($creatorCategory);
                    $user->getOrganizer()->setUser($user);
                    
                    $this->userManager->updateUser($user);
                    $this->addFlash('success', 'Organizer created');
                    return $this->redirectToRoute("dashboard_index");
                }
                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);
                $this->addFlash('error', 'Error. Contact admin.');
                if (null !== $response = $event->getResponse()) {
                    return $response;
                }
            }
            
            return $this->render('Dashboard/Administrator/User/Registration/admin-register-organizer.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }
