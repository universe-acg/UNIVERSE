<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Api;
    
    use App\Entity\User;
    use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
    
    class EmailChangeController extends AbstractController
    {
        private $entityManager;
        
        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }
        
        /**
         * @Route("/profile/change-email", name="email_change")
         */
        public function changeEmail(Request $request): Response
        {
            $user = $this->getUser(); // Get the current user
            $form = $this->getnewforminstance($user);
            
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    
                    // Check if the new email already exists in the database
                    $newEmail = $form->get('email')->getData();
                    $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $newEmail]);
                    
                    if ($existingUser) {
                        // Email already exists, handle this case
                        $this->addFlash('error', 'This email is already in use. Please choose a different email.');
                        $form = $this->getnewforminstance($user);
                    } elseif ($newEmail !== $user->getEmail()) {
                        // Check if the new email is different from the old one
                        if ($newEmail !== $user->getEmail()) {
                            // Update the user's email (you may want to add validation and error handling)
                            $user->setEmail($newEmail);
                            
                            // Save the updated user
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($user);
                            $entityManager->flush();
                            
                            // Refresh the user's data in the session to prevent logout
                            $this->get('security.token_storage')->getToken()->setUser($user);
                            
                            // Redirect or show a success message as needed
                            $this->addFlash('success', 'Email updated successfully');
                        }
                    }
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'This email is already in use. Please choose a different email.');
                    $token = $this->get('security.token_storage')->getToken();
                    $user = $token->getUser();
                    $this->get('security.token_storage')->setToken(new UsernamePasswordToken($user, null, 'main', $user->getRoles()));
                }
            }
            
            return $this->render('Dashboard/Shared/Account/change_email.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        
        private function getnewforminstance(User $user)
        {
            return $this->createFormBuilder(['email' => $user->getEmail()])
                ->add('email', TextType::class, [
                    'label' => 'New Email',
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Save Email',
                    'attr' => ['class' => 'btn btn-primary'],
                ])
                ->getForm();
        }
    }