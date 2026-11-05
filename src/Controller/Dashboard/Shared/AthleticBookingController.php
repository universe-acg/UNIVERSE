<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Entity\FacilityEntity;
use App\Form\FacilityType;
use App\Service\AppServices;
use App\Service\AthleticBookingService;
use App\Service\AthleticService;
use App\Service\FacilityService;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AthleticBookingController extends AbstractController
{
    private $bookingService;

    public function __construct(AthleticBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     *
     */
#[Route("/book/{facilityId}/{scheduleId}/{bookingDate}/{duration}", name: "book_reservation", methods: ["POST"])]
    public function bookReservation($facilityId, $scheduleId, $bookingDate, $duration): Response
    {

        $user = $this->getUser(); // Get the currently logged-in user

        $athleticBooking = $this->bookingService->createAthleticBooking($facilityId, $user->getId(), $scheduleId, $bookingDate, $duration);


        return $this->redirectToRoute('athletic_details', ['slug'=>$athleticBooking->getFacility()->getSlug()]);
    }

    /**
     *
     */
#[Route("/cancel/{athleticBookingId}", name: "cancel_reservation", methods: ["POST"])]
    public function cancelReservation($athleticBookingId): Response
    {

        $this->bookingService->cancelAthleticBooking($athleticBookingId);


        return $this->redirectToRoute('athletics');
    }

    /**
     *
     */
#[Route("/athletics/manage-facilities", name: "athletics_manage_facilities", methods: ["GET"])]
    public function manage(Request $request, PaginatorInterface $paginator, AppServices $appServices, AthleticService $athleticService)
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');

        $athleticfacilitys = $paginator->paginate($athleticService->getAthletics(array('keyword' => $keyword, 'hidden' => 'all')), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/Athletics/index.html.twig', [
            'facilities' => $athleticfacilitys,
        ]);
    }

    /**
     *
     *
     */
#[Route("/athletics/manage-facilities/add", name: "athletics_manage_facilities_add", methods: ["GET","POST"])]
#[Route("/athletics/manage-facilities/{slug}/edit", name: "athletics_manage_facilities_edit", methods: ["GET","POST"])]
    public function addedit(Request $request, AppServices $appServices, AthleticService $athleticService, FacilityService $facilityService, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $athleticfacility = new FacilityEntity();
        } else {
            $athleticfacilities = $athleticService->getAthletics(array('hidden' => 'all', 'slug' => $slug));

            if (empty($athleticfacilities)) {
                $this->addFlash('error', 'The Athletic Facility can not be found');
                return $appServices->redirectToReferer('athleticfacility');
            }

            $athleticfacility = $athleticfacilities[0];
        }

        $form = $this->createForm(FacilityType::class, $athleticfacility);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->handleImageUpload($athleticfacility);

                $em->persist($athleticfacility);
                $em->flush();

                if (!$slug) {
                    $this->addFlash('success', 'The athletic facility has been successfully created');
                } else {
                    $this->addFlash('success', 'The athletic facility has been successfully updated');
                }

                return $this->redirectToRoute("dashboard_administrator_athletics");
            }

            $this->addFlash('error', 'The form contains invalid data');
        }

        return $this->render('Dashboard/Shared/Athletics/add-edit.html.twig', array(
            "facility" => $athleticfacility,
            "form" => $form->createView(),
        ));
    }

    /**
     * Handle image upload and update the entity.
     *
     * @param FacilityEntity $athleticfacility
     */

    private function handleImageUpload(FacilityEntity $athleticfacility)
    {
        if ($athleticfacility->getMainImageFile()) {
            $mainImageFile = $athleticfacility->getMainImageFile();
            $athleticfacility->setMainImage($mainImageFile->getFileName()); // Update the entity with the file name
        }
    }

    /**
     *
     *
     */
#[Route("/athletics/manage-facilities/{slug}/disable", name: "athletics_manage_facilities_disable", methods: ["POST"])]
#[Route("/athletics/manage-facilities/{slug}/delete", name: "athletics_manage_facilities_delete", methods: ["POST"])]
    public function delete(AppServices $appServices, AthleticService $athleticService, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $athleticfacility = $athleticService->getAthletics(array('hidden' => 'all', 'slug' => $slug))[0];

        if (!$athleticfacility) {
            $this->addFlash('error', 'The Athletic Facility can not be found');
            return $appServices->redirectToReferer('athleticfacility');
        }





        if ($athleticfacility->getDeletedAt() !== null) {
            $this->addFlash('success', 'The Athletic Facility has been deleted');
        } else {
            $this->addFlash('success', 'The Athletic Facility has been deleted');
        }
        $em->remove($athleticfacility);
        $em->flush();
        return $appServices->redirectToReferer('athleticfacility');
    }

    /**
     *
     */
#[Route("/athletics/manage-facilities/{slug}/restore", name: "athletics_manage_facilities_restore", methods: ["POST"])]
    public function restore($slug, Request $request, AppServices $appServices, AthleticService $athleticService)
    {
        $athleticfacility = $athleticService->getAthletics(array('organizer' => "all", 'hidden' => 'all', 'slug' => $slug, "organizerEnabled" => "all"))[0];
        if (!$athleticfacility) {
            $this->addFlash('error', 'The Athletic Facility can not be found');
            return $appServices->redirectToReferer('athleticfacility');
        }
        $athleticfacility->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($athleticfacility);
        $em->flush();
        $this->addFlash('success', 'The Athletic Facility has been succesfully restored');

        return $appServices->redirectToReferer('athleticfacility');
    }

    /**
     *
     *
     */
#[Route("/athletics/manage-facilities/{slug}/show", name: "athletics_manage_facilities_show", methods: ["POST"])]
#[Route("/athletics/manage-facilities/{slug}/hide", name: "athletics_manage_facilities_hide", methods: ["POST"])]
    public function showhide(AppServices $appServices, AthleticService $athleticService, $slug)
    {
        $athleticfacility = $athleticService->getAthletics(array('hidden' => 'all', 'slug' => $slug))[0];

        if (!$athleticfacility) {
            $this->addFlash('error', 'The Athletic Facility can not be found');
            return $appServices->redirectToReferer('Athletic Facility');
        }
        if ($athleticfacility->isHidden() === true) {
            $athleticfacility->setHidden(false);
            $this->addFlash('success', 'The Athletic Facility is visible');
        } else {
            $athleticfacility->setHidden(true);
            $this->addFlash('error', 'The Athletic Facility is hidden');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($athleticfacility);
        $em->flush();
        return $appServices->redirectToReferer('athleticfacility');
    }

    /**
     *
     */
#[Route("/delete/{athleticBookingId}", name: "delete_reservation", methods: ["POST"])]
    public function deleteReservation($athleticBookingId): Response
    {

        $this->bookingService->deleteAthleticBooking($athleticBookingId);


        return $this->redirectToRoute('athletics');
    }
}
