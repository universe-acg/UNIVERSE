<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Controller\Api;

    use App\Entity\User;
    use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
    use Doctrine\ORM\EntityManagerInterface;
    use App\Controller\BaseController as AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

    class EmailChangeController extends AbstractController
    {
        private $entityManager;

        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }

        /**
         *
         */
#[Route("/profile/change-email", name: "email_change")]
        public function changeEmail(Request $request): Response
        {

            $user = $this->getUser();

            $form = $this->createEmailChangeForm($user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {

                    $newEmail = $form->get('email')->getData();

                    $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $newEmail]);

                    if ($existingUser && $existingUser->getId() !== $user->getId()) {

                        $this->addFlash('error', 'This email is already in use. Please choose a different email.');
                    } elseif ($newEmail !== $user->getEmail()) {


                        $user->setEmail($newEmail);

                        $this->entityManager->persist($user);
                        $this->entityManager->flush();

                        $this->refreshUserSession();

                        $this->addFlash('success', 'Email updated successfully');
                    }
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'This email is already in use. Please choose a different email.');
                }
            }

            return $this->render('Dashboard/Shared/Account/change_email.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        private function createEmailChangeForm(User $user)
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

        private function refreshUserSession()
        {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();
            $this->get('security.token_storage')->setToken(new UsernamePasswordToken($user, 'main', $user->getRoles()));
        }
    }
