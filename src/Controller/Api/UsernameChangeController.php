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

class UsernameChangeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/profile/change-username", name="username_change")
     */
    public function changeUsername(Request $request): Response
    {
        $user = $this->getUser(); // Get the current user

        $form = $this->getnewforminstance($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                // Check if the new username already exists in the database
                $newUsername = $form->get('username')->getData();
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $newUsername]);

                if ($existingUser) {
                    // Username already exists, handle this case
                    $this->addFlash('error', 'This username is already in use. Please choose a different username.');
                    $token = $this->get('security.token_storage')->getToken();
                    $user = $token->getUser();
                    $this->get('security.token_storage')->setToken(new UsernamePasswordToken($user, null, 'main', $user->getRoles()));
                    $form = $this->getnewforminstance($user);
                    return $this->render('Dashboard/Shared/Account/change_username.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }

                // Check if the new username is different from the old one
                if (($newUsername !== $user->getUsername()) && $newUsername !== $user->getUsername()) {
                    // Update the user's username (you may want to add validation and error handling)
                    $user->setUsername($newUsername);
                    $user->getOrganizer()->setSlug($newUsername);

                    // Save the updated user
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();

                    // Refresh the user's data in the session to prevent logout
                    $this->get('security.token_storage')->getToken()->setUser($user);

                    // Redirect or show a success message as needed
                    $this->addFlash('success', 'Username updated successfully');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'This username is already in use. Please choose a different username.');
                $token = $this->get('security.token_storage')->getToken();
                $user = $token->getUser();
                $this->get('security.token_storage')->setToken(new UsernamePasswordToken($user, null, 'main', $user->getRoles()));
            }
        }

        return $this->render('Dashboard/Shared/Account/change_username.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function getnewforminstance(User $user)
    {
        return $this->createFormBuilder(['username' => $user->getUsername()])
            ->add('username', TextType::class, [
                'label' => 'New Username',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Username',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->getForm();
    }
}