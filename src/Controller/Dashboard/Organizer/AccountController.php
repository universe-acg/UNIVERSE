<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

use App\Entity\Organizer;
use App\Form\OrganizerProfileType;
use App\Repository\UserRepository;
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends Controller
{

    /**
     *
     */
#[Route("/profile/{id}", name: "profile_admin", methods: ["GET", "POST"])]
    public function editOrganizerAsAdmin(Request $request, $id, UserRepository $userRepository)
    {


        $user = $userRepository->find($id);

        if (!$user || !$user->getOrganizer() instanceof Organizer) {
            throw $this->createNotFoundException('Organizer not found');
        }

        $organizer = $user->getOrganizer();

        $form = $this->createForm(OrganizerProfileType::class, $organizer);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($organizer);
                $em->flush();
                $this->addFlash('success', 'Their organizer profile has been successfully updated');
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }

        return $this->render('Dashboard/Organizer/Account/organizer_profile_asadmin.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     *
     */
#[Route("/profile", name: "profile", methods: ["GET", "POST"])]
    public function edit(Request $request)
    {

        $user = $this->getUser();

        if (!$user || !$user->getOrganizer() instanceof Organizer) {
            throw $this->createNotFoundException('Organizer not found');
        }

        $organizer = $user->getOrganizer();

        $form = $this->createForm(OrganizerProfileType::class, $organizer);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($organizer);
                $em->flush();
                $this->addFlash('success', 'Your organizer profile has been successfully updated');
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }

        return $this->render('Dashboard/Organizer/Account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
