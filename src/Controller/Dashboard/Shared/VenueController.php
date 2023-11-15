<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Entity\Venue;
use App\Form\VenueType;
use App\Service\AppServices;
use App\Service\VenueService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class VenueController extends Controller
{

    /**
     * @Route("/administrator/manage-venues", name="dashboard_administrator_venue", methods="GET")
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $appServices, VenueService $venueService, AuthorizationCheckerInterface $authChecker)
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');

        $venues = $paginator->paginate($venueService->getVenues(array('keyword' => $keyword, 'hidden' => 'all')), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/Venue/index.html.twig', [
            'venues' => $venues,
        ]);
    }

    /**
     * @Route("/administrator/manage-venues/add", name="dashboard_administrator_venue_add", methods="GET|POST")
     * @Route("/administrator/manage-venues/{slug}/edit", name="dashboard_administrator_venue_edit", methods="GET|POST")
     */
    public function addedit(Request $request, AppServices $appServices, VenueService $venueService, $slug = null, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();

        $organizer = "all";
        if ($authChecker->isGranted('ROLE_ORGANIZER')) {
            $organizer = $this->getUser()->getOrganizer()->getSlug();
        }

        if (!$slug) {
            $venue = new Venue();
        } else {
            $venue = $venueService->getVenues(array('hidden' => 'all', 'slug' => $slug))[0];
            if (!$venue) {
                $this->addFlash('error', 'The venue can not be found');
                return $appServices->redirectToReferer('venue');
            }
        }

        $form = $this->createForm(VenueType::class, $venue);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->handleImageUpload($venue);

                // Save event images
                foreach ($venue->getSecondaryImages() as $image) {
                    $venue->addSecondaryImage($image);
                }

                $venueService->removeSecondaryImagesByVenueId($venue->getId());

                $em->persist($venue);
                $em->flush();

                if (!$slug) {
                    $this->addFlash('success', 'The venue has been successfully created');
                } else {
                    $this->addFlash('success', 'The venue has been successfully updated');
                }
                if ($this->isGranted("ROLE_ADMINISTRATOR")) {
                    return $this->redirectToRoute("dashboard_administrator_venue");
                }
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }
        return $this->render('Dashboard/Shared/Venue/add-edit.html.twig', array(
            "venue" => $venue,
            "form" => $form->createView(),
        ));
    }

    /**
     * Handle image upload and update the entity.
     *
     * @param Venue $venue
     */
    private function handleImageUpload(Venue $venue)
    {
        if ($venue->getMainImageFile()) {
            $mainImageFile = $venue->getMainImageFile();
            $venue->setMainImage($mainImageFile->getFileName()); // Update the entity with the file name
        }
    }

    /**
     * @Route("/administrator/manage-venues/{slug}/disable", name="dashboard_administrator_venue_disable", methods="GET")
     * @Route("/administrator/manage-venues/{slug}/delete", name="dashboard_administrator_venue_delete", methods="GET")
     */
    public function delete(AppServices $appServices, VenueService $venueService, $slug, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();
        $venue = $venueService->getVenues(array('hidden' => 'all', 'slug' => $slug))[0];

        if (!$venue) {
            $this->addFlash('error', 'The venue can not be found');
            return $appServices->redirectToReferer('venue');
        }
//        if ($venue->getEventDatesCount() > 0) {
//            $this->addFlash('error', 'The venue can not be deleted because it is linked with one or more events');
//            return $appServices->redirectToReferer('venue');
//        }

        if ($venue->getDeletedAt() !== null) {
            $this->addFlash('success', 'The venue has been deleted');
        } else {
            $this->addFlash('success', 'The venue has been deleted');
        }
        $em->remove($venue);
        $em->flush();
        return $appServices->redirectToReferer('venue');
    }

    /**
     * @Route("/administrator/manage-venues/{slug}/restore", name="dashboard_administrator_venue_restore", methods="GET")
     */
    public function restore($slug, Request $request, AppServices $appServices, VenueService $venueService)
    {
        $venue = $venueService->getVenues(array('organizer' => "all", 'hidden' => 'all', 'slug' => $slug, "organizerEnabled" => "all"))->getQuery()->getOneOrNullResult();
        if (!$venue) {
            $this->addFlash('error', 'The venue can not be found');
            return $appServices->redirectToReferer('venue');
        }
        $venue->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($venue);
        $em->flush();
        $this->addFlash('success', 'The venue has been succesfully restored');

        return $appServices->redirectToReferer('venue');
    }

    /**
     * @Route("/administrator/manage-venues/{slug}/show", name="dashboard_administrator_venue_show", methods="GET")
     * @Route("/administrator/manage-venues/{slug}/hide", name="dashboard_administrator_venue_hide", methods="GET")
     */
    public function showhide(AppServices $appServices, VenueService $venueService, $slug, AuthorizationCheckerInterface $authChecker)
    {
        $venue = $venueService->getVenues(array('hidden' => 'all', 'slug' => $slug))[0];

        if (!$venue) {
            $this->addFlash('error', 'The venue can not be found');
            return $appServices->redirectToReferer('venue');
        }
        if ($venue->getHidden() === true) {
            $venue->setHidden(false);
            $this->addFlash('success', 'The venue is visible');
        } else {
            $venue->setHidden(true);
            $this->addFlash('error', 'The venue is hidden');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($venue);
        $em->flush();
        return $appServices->redirectToReferer('venue');
    }
}
