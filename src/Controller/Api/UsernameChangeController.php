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

class UsernameChangeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     *
     */
#[Route("/profile/change-username", name: "username_change")]
    public function changeUsername(Request $request): Response
    {
        $user = $this->getUser(); // Get the current user

        $form = $this->getnewforminstance($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $newUsername = $form->get('username')->getData();
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['usernameCanonical' => mb_strtolower($newUsername)]);

                if ($existingUser && $existingUser->getId() !== $user->getId()) {

                    $this->addFlash('error', 'This username is already in use. Please choose a different username.');
                    $token = $this->get('security.token_storage')->getToken();
                    $user = $token->getUser();
                    $this->get('security.token_storage')->setToken(new UsernamePasswordToken($user, 'main', $user->getRoles()));
                    $form = $this->getnewforminstance($user);
                    return $this->render('Dashboard/Shared/Account/change_username.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }

                if (($newUsername !== $user->getUsername()) && $newUsername !== $user->getUsername()) {

                    $user->setUsername($newUsername);
                    if ($user->getOrganizer()) $user->getOrganizer()->setSlug($newUsername);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->get('security.token_storage')->getToken()->setUser($user);

                    $this->addFlash('success', 'Username updated successfully');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'This username is already in use. Please choose a different username.');
                $token = $this->get('security.token_storage')->getToken();
                $user = $token->getUser();
                $this->get('security.token_storage')->setToken(new UsernamePasswordToken($user, 'main', $user->getRoles()));
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
