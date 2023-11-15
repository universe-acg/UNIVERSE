<?php

/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Administrator;

use App\Entity\ListingCategory;
use App\Form\ListingCategoryType;
use App\Service\AppServices;
use App\Service\ListingCategoryService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListingCategoryController extends Controller
{

    /**
     * @Route("/manage-listing-categories", name="listing_category", methods="GET")
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services, ListingCategoryService $listingCategoryService)
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
        $featured = ($request->query->get('featured')) == "" ? "all" : $request->query->get('featured');

        $categories = $paginator->paginate($listingCategoryService->getAllCategories(array('keyword' => $keyword, 'hidden' => 'all', 'featured' => $featured, 'sort' => 'createdAt', 'order' => 'DESC')), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Administrator/ListingCategory/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/manage-listing-categories/add", name="listing_category_add", methods="GET|POST")
     * @Route("/manage-listing-categories/{slug}/edit", name="listing_category_edit", methods="GET|POST")
     */
    public function addedit(Request $request, AppServices $services, $slug = null, ListingCategoryService $listingCategoryService)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $category = new ListingCategory();
        } else {
            $category = $listingCategoryService->getAllCategories(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
            if (!$category) {
                $this->addFlash('error', 'The category can not be found');
                return $services->redirectToReferer('listing_category');
            }
        }

        $form = $this->createForm(ListingCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($category);
                $em->flush();
                if (!$slug) {
                    $this->addFlash('success', 'The category has been successfully created');
                } else {
                    $this->addFlash('success', 'The category has been successfully updated');
                }
                return $this->redirectToRoute('dashboard_administrator_listing_category');
            } else {
                $formErrors = $form->getErrors(true, false);
                $this->addFlash('error', 'The form contains invalid data');
            }
        }
        return $this->render('Dashboard/Administrator/ListingCategory/add-edit.html.twig', array(
            "category" => $category,
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/manage-listing-categories/{slug}/disable", name="listing_category_disable", methods="GET")
     * @Route("/manage-listing-categories/{slug}/delete", name="listing_category_delete", methods="GET")
     */
    public function delete(AppServices $services, ListingCategoryService $listingCategoryService, $slug)
    {
        $category = $listingCategoryService->getAllCategories(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$category) {
            $this->addFlash('error', 'The category can not be found');
            return $services->redirectToReferer('listing_category');
        }
        if (count($category->getListings()) > 0) {
            $this->addFlash('error', 'The category can not be deleted because it is linked with one or more listings');
            return $services->redirectToReferer('listing_category');
        }
        if ($category->getDeletedAt() !== null) {
            $this->addFlash('success', 'The category has been deleted');
        } else {
            $this->addFlash('success', 'The category has been deleted');
        }
        $em = $this->getDoctrine()->getManager();
        $category->setFeatured(false);
        $category->setHidden(true);
        $em->persist($category);
        $em->flush();
        $em->remove($category);
        $em->flush();
        return $services->redirectToReferer('listing_category');
    }

    /**
     * @Route("/manage-listing-categories/{slug}/restore", name="listing_category_restore", methods="GET")
     */
    public function restore($slug, AppServices $services, ListingCategoryService $listingCategoryService)
    {
        $category = $listingCategoryService->getAllCategories(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$category) {
            $this->addFlash('error', 'The category can not be found');
            return $services->redirectToReferer('listing_category');
        }
        $category->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();
        $this->addFlash('success', 'The category has been successfully restored');

        return $services->redirectToReferer('listing_category');
    }

    /**
     * @Route("/manage-listing-categories/{slug}/show", name="listing_category_show", methods="GET")
     * @Route("/manage-listing-categories/{slug}/hide", name="listing_category_hide", methods="GET")
     */
    public function showhide(AppServices $services, $slug, ListingCategoryService $listingCategoryService)
    {
        $category = $listingCategoryService->getAllCategories(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$category) {
            $this->addFlash('error', 'The category can not be found');
            return $services->redirectToReferer('listing_category');
        }
        if ($category->getHidden() === true) {
            $category->setHidden(false);
            $this->addFlash('success', 'The category is visible');
        } else {
            $category->setFeatured(false);
            $category->setHidden(true);
            $this->addFlash('error', 'The category is hidden');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();
        return $services->redirectToReferer('listing_category');
    }
}
