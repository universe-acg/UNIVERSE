<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Dashboard\Administrator;
    
    use App\Form\EditAdminRegistrationOrganizerType;
    use App\Form\EditAdminRegistrationTypeAttendee;
    use App\Repository\UserRepository;
    use App\Service\AppServices;
    use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
    use Doctrine\ORM\EntityManagerInterface;
    use Knp\Component\Pager\PaginatorInterface;
    use PDOException;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class UserController extends Controller
    {
        private $entityManager;
        
        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }
        
        /**
         * @Route("/administrator/edit/user/{id}", name="user", methods="GET")
         */
        public function edituser(Request $request, $id, UserRepository $userRepository)
        {
            $user = $userRepository->find($id);
            
            if ($user->hasRole("ROLE_ORGANIZER")) {
                try {
                    $form = $this->createForm(EditAdminRegistrationOrganizerType::class, $user, [
                        'user' => $user,
                    ]);
                    $form->handleRequest($request);
                    
                    if ($form->isSubmitted()) {
                        try {
                            if ($form->isValid()) {
                                $em = $this->getDoctrine()->getManager();
                                $em->persist($user);
                                $em->flush();
                                $this->addFlash('success', 'Account has been updated');
                            } else {
                                $this->addFlash('error', 'The form contains invalid data');
                            }
                        } catch (UniqueConstraintViolationException $e) {
                            $this->addFlash('error', 'This email is already in use. Please choose a different email.');
                        }
                    }
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        // Check if it's a duplicate email entry
                        if (strpos($e->getMessage(), 'UNIQ_D01C6A22A0D96FBF') !== false) {
                            $this->addFlash('error', 'Duplicate email address. Please use a different email.');
                        } elseif (strpos($e->getMessage(), 'UNIQ_SOME_OTHER_CONSTRAINT') !== false) {
                            $this->addFlash('error', 'Duplicate username address. Please use a different username.');
                        } else {
                            $this->addFlash('error', 'An error occured.');
                        }
                    } else {
                        $this->addFlash('error', 'An error occured.');
                    }
                }
                
                return $this->render('Dashboard/Administrator/User/Registration/edit-admin-register-organizer.html.twig', array(
                    'form' => $form->createView(),
                ));
            }
            
            try {
                $form = $this->createForm(EditAdminRegistrationTypeAttendee::class, $user, [
                    'user' => $user,
                ]);
                $form->handleRequest($request);
                
                if ($form->isSubmitted()) {
                    if ($form->isValid()) {
                        try {
                            if ($form->isValid()) {
                                $em = $this->getDoctrine()->getManager();
                                $em->persist($user);
                                $em->flush();
                                $this->addFlash('success', 'Account has been updated');
                            } else {
                                $this->addFlash('error', 'The form contains invalid data');
                            }
                        } catch (UniqueConstraintViolationException $e) {
                            $this->addFlash('error', 'This email is already in use. Please choose a different email.');
                        }
                    } else {
                        $this->addFlash('error', 'The form contains invalid data');
                    }
                }
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    // Check if it's a duplicate email entry
                    if (strpos($e->getMessage(), 'UNIQ_D01C6A22A0D96FBF') !== false) {
                        $this->addFlash('error', 'Duplicate email address. Please use a different email.');
                    } elseif (strpos($e->getMessage(), 'UNIQ_SOME_OTHER_CONSTRAINT') !== false) {
                        $this->addFlash('error', 'Duplicate username address. Please use a different username.');
                    } else {
                        $this->addFlash('error', 'An error occured.');
                    }
                } else {
                    $this->addFlash('error', 'An error occured.');
                }
            }
            
            
            return $this->render('Dashboard/Administrator/User/Registration/edit-admin-register-attendee.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        
        
        /**
         * @Route("/manage-users", name="user", methods="GET")
         */
        public function index(Request $request, PaginatorInterface $paginator, AppServices $services)
        {
            
            $role = ($request->query->get('role')) == "" ? "all" : $request->query->get('role');
            $createdbyorganizerslug = ($request->query->get('createdbyorganizerslug')) == "" ? "all" : $request->query->get('createdbyorganizerslug');
            $organizername = ($request->query->get('organizername')) == "" ? "all" : $request->query->get('organizername');
            $username = ($request->query->get('username')) == "" ? "all" : $request->query->get('username');
            $email = ($request->query->get('email')) == "" ? "all" : $request->query->get('email');
            $firstname = ($request->query->get('firstname')) == "" ? "all" : $request->query->get('firstname');
            $lastname = ($request->query->get('lastname')) == "" ? "all" : $request->query->get('lastname');
            $enabled = ($request->query->get('enabled')) == "" ? "all" : $request->query->get('enabled');
            $users = $paginator->paginate($services->getUsers(array('role' => $role, 'createdbyorganizerslug' => $createdbyorganizerslug, 'organizername' => $organizername, 'username' => $username, 'email' => $email, 'firstname' => $firstname, 'lastname' => $lastname, 'enabled' => $enabled)), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));
            
            return $this->render('Dashboard/Administrator/User/index.html.twig', [
                'users' => $users
            ]);
        }
        
        /**
         * @Route("/manage-users/{slug}/delete-permanently", name="user_delete_permanently", methods="GET")
         * @Route("/manage-users/{slug}/delete", name="user_delete", methods="GET")
         */
        public function delete(AppServices $services, $slug)
        {
            $user = $services->getUsers(array('slug' => $slug, 'enabled' => 'all'))->getQuery()->getOneOrNullResult();
            
            if (!$user) {
                $this->addFlash('error', 'The user can not be found');
                return $services->redirectToReferer('user');
            }
            
            $em = $this->getDoctrine()->getManager();

// Check if the user has an associated organizer
            if ($user->getOrganizer()) {
                $organizer = $user->getOrganizer();
                
                // Remove the association from both sides
                $organizer->setUser(null);
                $user->setOrganizer(null);
                
                // Remove the organizer entity (if cascade={"remove"} is configured)
                $em->remove($organizer);
            }
            
            if ($user->getDeletedAt() !== null) {
                $this->addFlash('error', 'The user has been permanently deleted');
            } else {
                $this->addFlash('notice', 'The user has been deleted');
            }

// Remove the user entity itself
            $em->remove($user);
            $em->flush();
            
            return $services->redirectToReferer('user');
        }
        
        /**
         * @Route("/manage-users/{slug}/restore", name="user_restore", methods="GET")
         */
        public function restore($slug, AppServices $services)
        {
            
            $user = $services->getUsers(array('slug' => $slug, 'enabled' => 'all'))->getQuery()->getOneOrNullResult();
            if (!$user) {
                $this->addFlash('error', 'The user can not be found');
                return $services->redirectToReferer('user');
            }
            $user->setDeletedAt(null);
            $user->getOrganizer()->setDeletedAt(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'The user has been succesfully restored');
            
            return $services->redirectToReferer('user');
        }
        
        /**
         * @Route("/manage-users/{slug}/enable", name="user_enable", methods="GET")
         * @Route("/manage-users/{slug}/disable", name="user_disable", methods="GET")
         */
        public function enabledisable(AppServices $services, $slug)
        {
            
            $user = $services->getUsers(array('slug' => $slug, 'enabled' => 'all'))->getQuery()->getOneOrNullResult();
            if (!$user) {
                $this->addFlash('error', 'The user can not be found');
                return $services->redirectToReferer('page');
            }
            $em = $this->getDoctrine()->getManager();
            if ($user->isEnabled()) {
                $user->setEnabled(false);
                $this->addFlash('notice', 'The user has been disabled');
            } else {
                $user->setEnabled(true);
                $this->addFlash('success', 'The user has been enabled');
            }
            $em->persist($user);
            $em->flush();
            return $services->redirectToReferer('page');
        }
        
        /**
         * @Route("/administrator/manage-users/{slug}/more-information", name="user_information", methods="GET")
         */
        public function details(AppServices $services, $slug)
        {
            
            $user = $services->getUsers(array("slug" => $slug, "enabled" => "all"))->getQuery()->getOneOrNullResult();
            if (!$user) {
                return new Response('The user can not be found');
            }
            return $this->render('Dashboard/Administrator/User/information.html.twig', [
                'user' => $user,
            ]);
        }
    }
