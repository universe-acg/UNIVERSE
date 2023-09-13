<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrganizerController extends AbstractController
{

    /**
     * @Route("/organizer/{slug}", name="organizer")
     */
    public function organizer($slug, Request $request, AppServices $services, TranslatorInterface $translator): Response
    {

        $user = $services->getUsers(array('organizerslug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$user) {
            $this->addFlash('error', $translator->trans('The creator could not be found'));
            return $services->redirectToReferer("events");
        }

        $em = $this->getDoctrine()->getManager();
        $user->getOrganizer()->viewed();
        $em->persist($user->getOrganizer());
        $em->flush();

        return $this->render('Front/Organizer/profile.html.twig', ['organizer' => $user->getOrganizer()]
        );
    }

    /**
     * @Route("/creators", name="creators", methods="GET")
     */
    public function creators(Request $request, AppServices $services, PaginatorInterface $paginator): Response
    {
        $creatorsQueryBuilder = $services->getUsers(array('role' => 'ORGANIZER'));

        // Execute the query and fetch the results as an array of entities
        $creators = $creatorsQueryBuilder->getQuery()->getResult();

        // Create an array to hold users with the "ROLE_ORGANIZER" role
        $organizers = array();

        foreach ($creators as $creator) {
            $rolesArray = $creator->getRoles();

            if (in_array('ROLE_ORGANIZER', $rolesArray, true)) {
                $organizers[] = $creator;
            }
        }

        $pagination = $paginator->paginate(
            $organizers,               // Use the filtered $organizers array
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('Front/Organizer/organizers.html.twig', [
            'creators' => $pagination
        ]);
    }
}
