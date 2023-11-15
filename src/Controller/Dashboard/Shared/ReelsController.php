<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Entity\Reel;
use App\Form\ReelImageType;
use App\Form\ReelVideoType;
use App\Service\AppServices;
use App\Service\ReelService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReelsController extends Controller
{
    /**
     * @Route("/dashboard/organizer/manage-reels", name="dashboard_attendee_reel", methods="GET")
     * @Route("/dashboard/attendee/manage-reels", name="dashboard_organizer_reel", methods="GET")
     */
    public function index(Request $request, PaginatorInterface $paginator, ReelService $reelService, AuthorizationCheckerInterface $authChecker)
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');

        $reels = $paginator->paginate($reelService->getReels(array('keyword' => $keyword, 'hidden' => 'all')), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/Reel/index.html.twig', [
            'reels' => $reels,
        ]);
    }

    /**
     * @Route("/dashboard/attendee/manage-reels-image/add", name="dashboard_attendee_reel_add_image", methods="GET|POST")
     * @Route("/dashboard/attendee/manage-reels-image/{slug}/edit", name="dashboard_attendee_reel_edit_image", methods="GET|POST")
     */
    public function addeditimage(Request $request, AppServices $appServices, ReelService $reelService, $slug = null, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $reel = new Reel();
            $reel->setSlug($reel->generateUniqueSlug());
            $reel->setType("image");
            $reel->setUser($this->getUser());
        } else {
            $reel = $reelService->getReels(array('hidden' => 'all', 'slug' => $slug))[0];
            if (!$reel) {
                $this->addFlash('error', 'The reel can not be found');
                return $appServices->redirectToReferer('reel');
            }
        }

        $form = $this->createForm(ReelImageType::class, $reel);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->handleImageUpload($reel);
                $reel->setType("image");
                $em->persist($reel);
                $em->flush();
                if (!$slug) {
                    $this->addFlash('success', 'The reel has been successfully created');
                } else {
                    $this->addFlash('success', 'The reel has been successfully updated');

                    return $this->redirectToRouteAfterSubmission($authChecker);
                }
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }

        return $this->render('Dashboard/Shared/Reel/add-edit-image.html.twig', array(
            "reel" => $reel,
            "form" => $form->createView(),
        ));
    }

    /**
     * Handle image upload and update the entity.
     * @param Reel $reel
     */
    private function handleImageUpload(Reel $reel)
    {
        if ($reel->getMainImageFile()) {
            $mainImageFile = $reel->getMainImageFile();
            $reel->setMainImage($mainImageFile->getFileName()); // Update the entity with the file name
        }
    }

    /**
     * @Route("/dashboard/organizer/manage-reels-image/add", name="dashboard_organizer_reel_add_image", methods="GET|POST")
     * @Route("/dashboard/organizer/manage-reels-image/{slug}/edit", name="dashboard_organizer_reel_edit_image", methods="GET|POST")
     */
    public function addeditimageorg(Request $request, AppServices $appServices, ReelService $reelService, $slug = null, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $reel = new Reel();
            $reel->setSlug($reel->generateUniqueSlug());
            $reel->setType("image");
            $reel->setUser($this->getUser());
        } else {
            $reel = $reelService->getReels(array('hidden' => 'all', 'slug' => $slug))[0];
            if (!$reel) {
                $this->addFlash('error', 'The reel can not be found');
                return $appServices->redirectToReferer('reel');
            }
        }

        $form = $this->createForm(ReelImageType::class, $reel);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->handleImageUpload($reel);
                $reel->setType("image");
                $em->persist($reel);
                $em->flush();
                if (!$slug) {
                    $this->addFlash('success', 'The reel has been successfully created');
                } else {
                    $this->addFlash('success', 'The reel has been successfully updated');

                    return $this->redirectToRouteAfterSubmission($authChecker);
                }
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }
        return $this->render('Dashboard/Shared/Reel/add-edit-image.html.twig', array(
            "reel" => $reel,
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/dashboard/attendee/manage-reels-video/add", name="dashboard_attendee_reel_add_video", methods="GET|POST")
     * @Route("/dashboard/attendee/manage-reels-video/{slug}/edit", name="dashboard_attendee_reel_edit_video", methods="GET|POST")
     */
    public function addeditvideo(Request $request, AppServices $appServices, ReelService $reelService, $slug = null, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $reel = new Reel();
            $reel->setSlug($reel->generateUniqueSlug());
            $reel->setType("video");
            $reel->setUser($this->getUser());
        } else {
            $reel = $reelService->getReels(array('hidden' => 'all', 'slug' => $slug))[0];
            if (!$reel) {
                $this->addFlash('error', 'The reel can not be found');
                return $appServices->redirectToReferer('reel');
            }
        }

        $form = $this->createForm(ReelVideoType::class, $reel);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->handleImageUpload($reel);
                $this->handleVideoUpload($reel);

                $reel->setType("video");
                $em->persist($reel);
                $em->flush();

                if (!$slug) {
                    $this->addFlash('success', 'The reel has been successfully created');
                } else {
                    $this->addFlash('success', 'The reel has been successfully updated');
                }
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }
        return $this->render('Dashboard/Shared/Reel/add-edit-video.html.twig', array(
            "reel" => $reel,
            "form" => $form->createView(),
        ));
    }

    private function handleVideoUpload(Reel $reel): void
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reel);
        $entityManager->flush();
    }

    /**
     * @Route("/dashboard/organizer/manage-reels-video/add", name="dashboard_organizer_reel_add_video", methods="GET|POST")
     * @Route("/dashboard/organizer/manage-reels-video/{slug}/edit", name="dashboard_organizer_reel_edit_video", methods="GET|POST")
     */
    public function addeditvideoorg(Request $request, AppServices $appServices, ReelService $reelService, $slug = null, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $reel = new Reel();
            $reel->setSlug($reel->generateUniqueSlug());
            $reel->setType("video");
            $reel->setUser($this->getUser());
        } else {
            $reel = $reelService->getReels(array('hidden' => 'all', 'slug' => $slug))[0];
            if (!$reel) {
                $this->addFlash('error', 'The reel can not be found');
                return $appServices->redirectToReferer('reel');
            }
        }

        $form = $this->createForm(ReelVideoType::class, $reel);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->handleImageUpload($reel);
                $this->handleVideoUpload($reel);

                $reel->setType("video");
                $em->persist($reel);
                $em->flush();

                if (!$slug) {
                    $this->addFlash('success', 'The reel has been successfully created');
                } else {
                    $this->addFlash('success', 'The reel has been successfully updated');
                }
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }
        return $this->render('Dashboard/Shared/Reel/add-edit-video.html.twig', array(
            "reel" => $reel,
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/dashboard/organizer/manage-reels/{slug}/disable", name="dashboard_attendee_reel_disable", methods="GET")
     * @Route("/dashboard/organizer/manage-reels/{slug}/delete", name="dashboard_attendee_reel_delete", methods="GET")
     * @Route("/dashboard/attendee/manage-reels/{slug}/disable", name="dashboard_organizer_reel_disable", methods="GET")
     * @Route("/dashboard/attendee/manage-reels/{slug}/delete", name="dashboard_organizer_reel_delete", methods="GET")
     */
    public function delete(AppServices $appServices, ReelService $reelService, $slug, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();
        $reel = $reelService->getReels(array('hidden' => 'all', 'slug' => $slug))[0];

        if (!$reel) {
            $this->addFlash('error', 'The reel can not be found');
            return $appServices->redirectToReferer('reel');
        }

        if ($reel->getDeletedAt() !== null) {
            $this->addFlash('success', 'The reel has been deleted');
        } else {
            $this->addFlash('success', 'The reel has been deleted');
        }

        $em->remove($reel);
        $em->flush();
        return $appServices->redirectToReferer('reel');
    }

    /**
     * @Route("/dashboard/organizer/manage-reels/{slug}/restore", name="dashboard_attendee_reel_restore", methods="GET")
     * @Route("/dashboard/attendee/manage-reels/{slug}/restore", name="dashboard_organizer_reel_restore", methods="GET")
     */
    public function restore($slug, Request $request, AppServices $appServices, ReelService $reelService)
    {
        $reel = $reelService->getReels(array('organizer' => "all", 'hidden' => 'all', 'slug' => $slug, "organizerEnabled" => "all"))->getQuery()->getOneOrNullResult();
        if (!$reel) {
            $this->addFlash('error', 'The reel can not be found');
            return $appServices->redirectToReferer('reel');
        }
        $reel->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($reel);
        $em->flush();
        $this->addFlash('success', 'The reel has been succesfully restored');

        return $appServices->redirectToReferer('reel');
    }

    /**
     * @Route("/dashboard/organizer/manage-reels/{slug}/hide", name="dashboard_organizer_reel_showhide", methods="GET")
     * @Route("/dashboard/attendee/manage-reels/{slug}/show", name="dashboard_attendee_reel_showhide", methods="GET")
     */
    public function showhide(AppServices $appServices, ReelService $reelService, $slug, AuthorizationCheckerInterface $authChecker)
    {
        $reel = $reelService->getReels(array('hidden' => 'all', 'slug' => $slug))[0];

        if (!$reel) {
            $this->addFlash('error', 'The reel can not be found');
            return $appServices->redirectToReferer('reel');
        }
        if ($reel->getHidden() === true) {
            $reel->setHidden(false);
            $this->addFlash('success', 'The reel is visible');
        } else {
            $reel->setHidden(true);
            $this->addFlash('error', 'The reel is hidden');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($reel);
        $em->flush();
        return $appServices->redirectToReferer('reel');
    }
}
