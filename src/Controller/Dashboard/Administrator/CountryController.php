<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Administrator;

use App\Entity\Country;
use App\Form\CountryType;
use App\Service\AppServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends Controller
{

    /**
     * @Route("/manage-countries", name="country", methods="GET")
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services): Response
    {
        $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');

        $countries = $paginator->paginate($services->getCountries(array('keyword' => $keyword, 'hidden' => 'all', 'sort' => 'c.createdAt', 'order' => 'DESC')), $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Administrator/Country/index.html.twig', [
            'countries' => $countries,
        ]);
    }

    /**
     * @Route("/manage-countries/add", name="country_add", methods="GET|POST")
     * @Route("/manage-countries/{slug}/edit", name="country_edit", methods="GET|POST")
     */
    public function addedit(Request $request, AppServices $services, $slug = null): Response
    {
        $em = $this->getDoctrine()->getManager();

        if (!$slug) {
            $country = new Country();
        } else {
            $country = $services->getCountries(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
            if (!$country) {
                $this->addFlash('error', $translator->trans('The country can not be found'));
                return $services->redirectToReferer('country');
            }
        }

        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($country);
                $em->flush();
                if (!$slug) {
                    $this->addFlash('success', $translator->trans('The country has been successfully created'));
                } else {
                    $this->addFlash('success', $translator->trans('The country has been successfully updated'));
                }
                return $this->redirectToRoute('dashboard_administrator_country');
            }

            $this->addFlash('error', $translator->trans('The form contains invalid data'));
        }
        return $this->render('Dashboard/Administrator/Country/add-edit.html.twig', array(
            "country" => $country,
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/manage-countries/{slug}/disable", name="country_disable", methods="GET")
     * @Route("/manage-countries/{slug}/delete", name="country_delete", methods="GET")
     */
    public function delete(AppServices $services, $slug): RedirectResponse
    {

        $country = $services->getCountries(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$country) {
            $this->addFlash('error', $translator->trans('The country can not be found'));
            return $services->redirectToReferer('country');
        }
        if (count($country->getEvents()) > 0) {
            $this->addFlash('error', $translator->trans('The country can not be deleted because it is linked with one or more events'));
            return $services->redirectToReferer('country');
        }
        if ($country->getDeletedAt() !== null) {
            $this->addFlash('error', $translator->trans('The country has been deleted'));
        } else {
            $this->addFlash('notice', $translator->trans('The country has been disabled'));
        }
        $em = $this->getDoctrine()->getManager();
        $country->setHidden(true);
        $em->persist($country);
        $em->flush();
        $em->remove($country);
        $em->flush();
        return $services->redirectToReferer('country');
    }

    /**
     * @Route("/manage-countries/{slug}/restore", name="country_restore", methods="GET")
     */
    public function restore($slug, Request $request, AppServices $services): RedirectResponse
    {

        $country = $services->getCountries(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$country) {
            $this->addFlash('error', $translator->trans('The country can not be found'));
            return $services->redirectToReferer('country');
        }
        $country->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($country);
        $em->flush();
        $this->addFlash('success', $translator->trans('The country has been succesfully restored'));

        return $services->redirectToReferer('country');
    }

    /**
     * @Route("/manage-countries/{slug}/show", name="country_show", methods="GET")
     * @Route("/manage-countries/{slug}/hide", name="country_hide", methods="GET")
     */
    public function showhide(AppServices $services, $slug): RedirectResponse
    {

        $country = $services->getCountries(array('hidden' => 'all', 'slug' => $slug))->getQuery()->getOneOrNullResult();
        if (!$country) {
            $this->addFlash('error', $translator->trans('The country can not be found'));
            return $services->redirectToReferer('country');
        }
        if ($country->getHidden() === true) {
            $country->setHidden(false);
            $this->addFlash('success', $translator->trans('The country is visible'));
        } else {
            $country->setHidden(true);
            $this->addFlash('error', $translator->trans('The country is hidden'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($country);
        $em->flush();
        return $services->redirectToReferer('country');
    }

}
