<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Form\AccountSettingsType;
use App\Form\OrganizerAccountSettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends Controller
{

    /**
     * @Route("/attendee/account/settings", name="dashboard_attendee_account_settings")
     */
    public function settings(Request $request)
    {

        $form = $this->createForm(AccountSettingsType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($this->getUser());
                $em->flush();
                $this->addFlash('success', 'Your account settings have been updated');
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }

        return $this->render('Dashboard/Shared/Account/settings.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/organizer/account/settings", name="dashboard_organizer_account_settings")
     */
    public function settingsorganizer(Request $request)
    {

        $form = $this->createForm(OrganizerAccountSettingsType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($this->getUser());
                $em->flush();
                $this->addFlash('success', 'Your account settings have been updated');
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }

        return $this->render('Dashboard/Shared/Account/account-settings.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
