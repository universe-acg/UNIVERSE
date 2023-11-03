<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Administrator;

use App\Entity\Amenity;
use App\Form\AmenityType;
use App\Service\AmenityService;
use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AmenityController extends Controller
{

    /**
     * @Route("/manage-amenities", name="amenity", methods="GET")
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services, AmenityService $amenityService)
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');

        $amenities = $paginator->paginate($amenityService->getAmenities(array('keyword' => $keyword, 'hidden' => 'all', 'sort' => 'createdAt', 'order' => 'DESC')), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Administrator/Amenity/index.html.twig', [
            'amenities' => $amenities,
        ]);
    }

    /**
     * @Route("/manage-amenities/add", name="amenity_add", methods="GET|POST")
     * @Route("/manage-amenities/{slug}/edit", name="amenity_edit", methods="GET|POST")
     */
    public function addedit(Request $request, AppServices $services, AmenityService $amenityService, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $amenity = new Amenity();
        } else {
            $amenity = $amenityService->getAmenityBySlug($slug);
            if (!$amenity) {
                $this->addFlash('error', 'The amenity can not be found');
                return $services->redirectToReferer('amenity');
            }
        }

        $form = $this->createForm(AmenityType::class, $amenity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($amenity);
                $em->flush();
                if (!$slug) {
                    $this->addFlash('success', 'The amenity has been successfully created');
                } else {
                    $this->addFlash('success', 'The amenity has been successfully updated');
                }
                return $this->redirectToRoute('dashboard_administrator_amenity');
            }

            $this->addFlash('error', 'The form contains invalid data');
        }
        return $this->render('Dashboard/Administrator/Amenity/add-edit.html.twig', array(
            "amenity" => $amenity,
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/manage-amenities/{slug}/disable", name="amenity_disable", methods="GET")
     * @Route("/manage-amenities/{slug}/delete", name="amenity_delete", methods="GET")
     */
    public function delete(AppServices $services, AmenityService $amenityService, $slug)
    {

        $amenity = $amenityService->getAmenityBySlug($slug);
        if (!$amenity) {
            $this->addFlash('error', 'The amenity can not be found');
            return $services->redirectToReferer('amenity');
        }
        if (count($amenity->getVenues()) > 0) {
            $this->addFlash('error', 'The amenity can not be deleted because it is linked with one or more venues');
            return $services->redirectToReferer('amenity');
        }
//        if ($amenity->getDeletedAt() !== null) {
//            $this->addFlash('error', 'The amenity has been deleted');
//        } else {
        $this->addFlash('notice', 'The amenity has been deleted');
//        }
        $em = $this->getDoctrine()->getManager();
        $amenity->setHidden(true);
        $em->persist($amenity);
        $em->flush();
        $em->remove($amenity);
        $em->flush();
        return $services->redirectToReferer('amenity');
    }

    /**
     * @Route("/manage-amenities/{slug}/restore", name="amenity_restore", methods="GET")
     */
    public function restore($slug, Request $request, AppServices $services, AmenityService $amenityService)
    {

        $amenity = $amenityService->getAmenityBySlug($slug);
        if (!$amenity) {
            $this->addFlash('error', 'The amenity can not be found');
            return $services->redirectToReferer('amenity');
        }
        $amenity->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($amenity);
        $em->flush();
        $this->addFlash('success', 'The amenity has been succesfully restored');

        return $services->redirectToReferer('amenity');
    }

    /**
     * @Route("/manage-amenities/{slug}/show", name="amenity_show", methods="GET")
     * @Route("/manage-amenities/{slug}/hide", name="amenity_hide", methods="GET")
     */
    public function showhide(AppServices $services, AmenityService $amenityService, $slug)
    {

        $amenity = $amenityService->getAmenityBySlug($slug);
        if (!$amenity) {
            $this->addFlash('error', 'The amenity can not be found');
            return $services->redirectToReferer('amenity');
        }
        if ($amenity->isHidden() === true) {
            $amenity->setHidden(false);
            $this->addFlash('success', 'The amenity is visible');
        } else {
            $amenity->setHidden(true);
            $this->addFlash('error', 'The amenity is hidden');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($amenity);
        $em->flush();
        return $services->redirectToReferer('amenity');
    }

}
