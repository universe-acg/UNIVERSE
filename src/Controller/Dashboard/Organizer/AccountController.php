<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

use App\Entity\Organizer;
use App\Form\OrganizerProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends Controller
{

    /**
     * @Route("/profile", name="profile", methods={"GET", "POST"})
     */
    public function edit(Request $request)
    {
        // Retrieve the user
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
                $this->addFlash('success', $translator->trans('Your organizer profile has been successfully updated'));
            } else {
                $this->addFlash('error', $translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('Dashboard/Organizer/Account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
