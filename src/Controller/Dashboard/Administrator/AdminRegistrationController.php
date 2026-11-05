<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Controller\Dashboard\Administrator;

    use App\Form\AdminRegistrationOrganizerType;
    use App\Form\AdminRegistrationTypeAttendee;
    use App\Service\AppServices;
    use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
    use Doctrine\ORM\EntityManagerInterface;



    use App\Security\UserManager as UserManagerInterface;
    use App\Controller\BaseController as Controller;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

    class AdminRegistrationController extends Controller
    {

        private $eventDispatcher;
        private $formFactory;
        private $userManager;
        private $tokenStorage;
        private $services;

        private $entityManager;

        public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage, AppServices $services)
        {
            $this->eventDispatcher = $eventDispatcher;
            $this->userManager = $userManager;
            $this->entityManager = $entityManager;
            $this->tokenStorage = $tokenStorage;
            $this->services = $services;
        }

        /**
         *
         */
#[Route("/administrator/signup/attendee", name: "registration_register_attendee_admin")]
        public function registerAttendeeAction(Request $request): Response
        {
            $this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');
            $user = $this->userManager->createUser();

            $form = $this->createForm(AdminRegistrationTypeAttendee::class, $user);

            $form->setData($user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {

                    if ($form->isValid()) {

                        $user->addRole('ROLE_ATTENDEE');
                        $user->setEnabled(true);

                        $this->userManager->updateUser($user);
                        $this->addFlash('success', 'Attendee created');
                        return $this->redirectToRoute("dashboard_index");
                    }

                    $this->addFlash('error', 'Error. Contact admin.');
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'This email is already in use. Please choose a different email.');
                }
            }

            return $this->render('Dashboard/Administrator/User/Registration/admin-register-attendee.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        /**
         *
         */
#[Route("/administrator/signup/organizer", name: "registration_register_organizer_admin")]
        public function registerOrganizerAction(Request $request): Response
        {
            $this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');
            $user = $this->userManager->createUser();

            $form = $this->createForm(AdminRegistrationOrganizerType::class, $user);

            $form->setData($user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {

                    if ($form->isValid()) {

                        $user->addRole('ROLE_ORGANIZER');
                        $user->setEnabled(true);
                        $creatorCategory = $form->get('organizer')->get('category')->getData();
                        $user->getOrganizer()->setCategory($creatorCategory);
                        $user->getOrganizer()->setUser($user);

                        $this->userManager->updateUser($user);
                        $this->addFlash('success', 'Organizer created');
                        return $this->redirectToRoute("dashboard_index");
                    }

                    $this->addFlash('error', 'Error. Contact admin.');
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'This email is already in use. Please choose a different email.');
                }
            }

            return $this->render('Dashboard/Administrator/User/Registration/admin-register-organizer.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }
