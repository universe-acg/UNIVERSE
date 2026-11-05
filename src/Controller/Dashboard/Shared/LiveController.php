<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Entity\Live;
use App\Form\LiveType;
use App\Service\AppServices;
use App\Service\LiveService;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class LiveController extends Controller
{
    /**
     *
     */
#[Route("/organizer/manage-lives", name: "dashboard_organizer_live", methods: ["GET"])]
    public function index(Request $request, PaginatorInterface $paginator, LiveService $liveService)
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');

        $lives = $paginator->paginate($liveService->getLives(array('keyword' => $keyword, 'hidden' => 'all')), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/Live/index.html.twig', [
            'lives' => $lives,
        ]);
    }

    /**
     *
     *
     */
#[Route("/organizer/manage-lives/add", name: "dashboard_organizer_live_add", methods: ["GET","POST"])]
#[Route("/organizer/manage-lives/{slug}/edit", name: "dashboard_organizer_live_edit", methods: ["GET","POST"])]
    public function addedit(Request $request, AppServices $appServices, LiveService $liveService, AuthorizationCheckerInterface $authChecker, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $live = new Live();
        } else {
            $live = $liveService->getLives(array('hidden' => 'all', 'slug' => $slug))[0];
            if (!$live) {
                $this->addFlash('error', 'The live can not be found');
                return $appServices->redirectToReferer('live');
            }
        }

        $form = $this->createForm(LiveType::class, $live);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->handleImageUpload($live);

                $em->persist($live);
                $em->flush();

                if (!$slug) {
                    $this->addFlash('success', 'The live has been successfully created');
                } else {
                    $this->addFlash('success', 'The live has been successfully updated');
                }
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }
        return $this->render('Dashboard/Shared/Live/add-edit.html.twig', array(
            "live" => $live,
            "form" => $form->createView(),
        ));
    }

    /**
     * Handle image upload and update the entity.
     *
     * @param Live $live
     */

    private function handleImageUpload(Live $live)
    {
        if ($live->getMainImageFile()) {
            $mainImageFile = $live->getMainImageFile();
            $live->setMainImage($mainImageFile->getFileName()); // Update the entity with the file name
        }
    }

    /**
     *
     *
     */
#[Route("/organizer/manage-lives/{slug}/disable", name: "dashboard_organizer_live_disable", methods: ["POST"])]
#[Route("/organizer/manage-lives/{slug}/delete", name: "dashboard_organizer_live_delete", methods: ["POST"])]
    public function delete(AppServices $appServices, LiveService $liveService, $slug, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();
        $live = $liveService->getLives(array('hidden' => 'all', 'slug' => $slug))[0];

        if (!$live) {
            $this->addFlash('error', 'The live can not be found');
            return $appServices->redirectToReferer('live');
        }

        if ($live->getDeletedAt() !== null) {
            $this->addFlash('success', 'The live has been deleted');
        } else {
            $this->addFlash('success', 'The live has been deleted');
        }

        $em->remove($live);
        $em->flush();
        return $appServices->redirectToReferer('live');
    }

    /**
     *
     */
#[Route("/organizer/manage-lives/{slug}/restore", name: "dashboard_organizer_live_restore", methods: ["POST"])]
    public function restore($slug, Request $request, AppServices $appServices, LiveService $liveService)
    {
        $live = $liveService->getLives(array('organizer' => "all", 'hidden' => 'all', 'slug' => $slug, "organizerEnabled" => "all"))->getQuery()->getOneOrNullResult();
        if (!$live) {
            $this->addFlash('error', 'The live can not be found');
            return $appServices->redirectToReferer('live');
        }
        $live->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($live);
        $em->flush();
        $this->addFlash('success', 'The live has been succesfully restored');

        return $appServices->redirectToReferer('live');
    }

    /**
     *
     *
     */
#[Route("/organizer/manage-lives/{slug}/show", name: "dashboard_organizer_live_show", methods: ["POST"])]
#[Route("/organizer/manage-lives/{slug}/hide", name: "dashboard_organizer_live_hide", methods: ["POST"])]
    public function showhide(AppServices $appServices, LiveService $liveService, $slug, AuthorizationCheckerInterface $authChecker)
    {
        $live = $liveService->getLives(array('hidden' => 'all', 'slug' => $slug))[0];

        if (!$live) {
            $this->addFlash('error', 'The live can not be found');
            return $appServices->redirectToReferer('live');
        }
        if ($live->getHidden() === true) {
            $live->setHidden(false);
            $this->addFlash('success', 'The live is visible');
        } else {
            $live->setHidden(true);
            $this->addFlash('error', 'The live is hidden');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($live);
        $em->flush();
        return $appServices->redirectToReferer('live');
    }
}
