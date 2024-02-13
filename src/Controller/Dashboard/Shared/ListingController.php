<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Entity\Listing;
use App\Form\ListingType;
use App\Form\ListingTypeSpecific;
use App\Repository\CategoryRepository;
use App\Repository\ListingFileRepository;
use App\Repository\ListingRepository;
use App\Repository\OrderRepository;
use App\Service\AppServices;
use App\Service\EventService;
use App\Service\ListingService;
use App\Service\ListingSubmissionService;
use App\Service\NotificationService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use InvalidArgumentException;
use Knp\Component\Pager\PaginatorInterface;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig_Environment;

class ListingController extends Controller
{
    private $listingRepository;

    private $messageBus;

    private $notificationService;

    public function __construct(NotificationService $notificationService, ListingRepository $listingRepository, MessageBusInterface $messageBus)
    {
        $this->listingRepository = $listingRepository;
        $this->messageBus = $messageBus;
        $this->notificationService = $notificationService;
    }

    /**
     * @Route("/dashboard/organizer/manage-listings", name="dashboard_organizer_listing", methods="GET")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ListingService $listingService
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator, ListingService $listingService): Response
    {
        $organizerid = $this->getUser()->getOrganizer()->getId();
        $listings = $listingService->getMyListings(array("organizerid" => $organizerid, "sort" => "e.createdAt", "order" => "DESC"));
        $listings = $paginator->paginate($listings, $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/Listing/index.html.twig', [
            'listings' => $listings,
        ]);
    }

    /**
     * @Route("/dashboard/attendee/my-listing-applications", name="mylistingapplicaitons", methods="GET")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ListingService $listingService
     * @return Response
     */
    public function mylistingapplicaitons(Request $request, PaginatorInterface $paginator, ListingService $listingService, ListingSubmissionService $listingSubmissionService): Response
    {
        $em = $this->getDoctrine()->getManager();

        $listingssubs = $listingSubmissionService->findActiveSubmissionsByUser($this->getUser(), $em);
        $listingssubs = $paginator->paginate($listingssubs, $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/ListingSubmissions/index.html.twig', [
            'listingsubs' => $listingssubs,
        ]);
    }

    /**
     * @Route("/dashboard/organizer/my-listing-applications-creator/{id}", name="mylistingapplicaitonscreator", methods="GET")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ListingService $listingService
     * @param ListingSubmissionService $listingSubmissionService
     * @return Response
     */
    public function mylistingapplicaitonscreator(Request $request, $id, PaginatorInterface $paginator, ListingService $listingService, ListingSubmissionService $listingSubmissionService): Response
    {
        $em = $this->getDoctrine()->getManager();

        $listingssubs = $listingSubmissionService->findActiveSubmissionsByListingId($id, $em);
        $listingssubs = $paginator->paginate($listingssubs, $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/ListingSubmissions/index.html.twig', [
            'listingsubs' => $listingssubs,
        ]);
    }

    /**
     * @Route("/dashboard/organizer/my-listings/add", name="dashboard_organizer_listing_add", methods="GET|POST")
     * @throws Exception|InvalidArgumentException
     */
    public function addListing(
        Request                       $request,
        AppServices                   $services,
        ListingService                $listingService,
        EventService                  $eventService,
                                      $slug = null,
        AuthorizationCheckerInterface $authChecker
    )
    {
        $listing = $this->initializeListing($slug);
        if ($listing === null) {
            return $services->redirectToReferer('listing');
        }

        $userEvents = $eventService->getEvents(['organizer' => $this->getUser()->getOrganizer()]);

        $formTypegeneral = ListingType::class;
        $formgeneral = $this->createForm($formTypegeneral, $listing, [
            'validation_groups' => ['create', 'Default'],
        ]);

        $em = $this->getDoctrine()->getManager();
        $formgeneral->handleRequest($request);

        if ($formgeneral->isSubmitted() && $formgeneral->isValid()) {
            $this->processSubmittedListing($listing, $em, $services);

            // Handle new listing creation or update
            if (!$slug) {
                $this->addFlash('success', 'The listing has been successfully created');
            } else {
                $this->addFlash('success', 'The listing has been successfully updated');
            }

            return $this->redirectToRouteAfterSubmission($authChecker);
        }


        $formTypespecific = ListingTypeSpecific::class;
        $formspecific = $this->createForm($formTypespecific, $listing, [
            'validation_groups' => ['create', 'Default'],
            'user_events' => $userEvents,
        ]);

        $em = $this->getDoctrine()->getManager();
        $formspecific->handleRequest($request);

        if ($formspecific->isSubmitted() && $formspecific->isValid()) {
            $this->processSubmittedListing($listing, $em, $services);

            // Handle new listing creation or update
            if (!$slug) {
                $this->addFlash('success', 'The listing has been successfully created');
            } else {
                $this->addFlash('success', 'The listing has been successfully updated');
            }

            return $this->redirectToRouteAfterSubmission($authChecker);
        }

        return $this->renderListingForm($listing, $formgeneral, $formspecific);
    }

    private function initializeListing($slug)
    {
        if (!$slug) {
            // If no slug is provided, create a new listing
            $listing = new Listing();
        } else {
            $listing = $this->getListingBySlug($slug);

            if (!$listing) {
                $this->addFlash('error', 'The listing can not be found');
                return null;
            }
        }

        return $listing;
    }

    public function getListingBySlug($slug)
    {
        $listing = $this->listingRepository->findOneBySlug(['slug' => $slug]);

        if (!$listing) {
            return null;
        }

        return $listing;
    }

    private function processSubmittedListing($listing, $em, $services)
    {
        $listing->setOrganizer($this->getUser()->getOrganizer());
        $newSlug = $this->generateUniqueSlug($listing->getName());
        $listing->setSlug($newSlug);
        $listing->setPublished(true);

        $em->persist($listing);
        $em->flush();

        $this->setimagesanddates($listing, $em, $services, $newSlug);
    }

    private function generateUniqueSlug($title)
    {
        // Generate a slug based on the title
        $baseSlug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));

        // Generate a random string (e.g., 6 characters) to append to the slug
        $randomString = bin2hex(random_bytes(10)); // Adjust the number of bytes for desired length

        // Combine the base slug and random string
        $uniqueSlug = $baseSlug . '-' . $randomString;

        // Check if the generated slug already exists in the database
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Listing::class);

        // Keep incrementing the random string until you find a unique slug
        $counter = 1;
        while ($repository->findOneBy(['slug' => $uniqueSlug])) {
            $counter++;
            $uniqueSlug = $baseSlug . '-' . $randomString . '-' . $counter;
        }

        return $uniqueSlug;
    }

    private function setimagesanddates($listing, $em, $services, $slug): void
    {
        foreach ($listing->getImages() as $image) {
            $listing->addImage($image);
            $em->persist($image);
            $em->flush();
        }
//        foreach ($listing->getListingdates() as $listingdate) {
//            $listingdate->setListing($listing);
//            if (!$slug || !$listingdate->getReference()) {
//                $listingdate->setReference($services->generateReference(10));
//            }
//        }
    }

    // Helper method to get category by name

    private function redirectToRouteAfterSubmission($authChecker)
    {
        return $this->redirectToRoute("dashboard_organizer_listing");
    }

    private function renderListingForm($listing, $formgeneral, $formspecific)
    {
        return $this->render('Dashboard/Shared/Listing/add-edit.html.twig', [
            "listing" => $listing,
            "formgeneral" => $formgeneral->createView(),
            "formspecific" => $formspecific->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/organizer/my-listings/{slug}/edit", name="dashboard_organizer_listing_edit", methods="GET|POST")
     * @throws InvalidDatabaseException
     * @throws Exception
     */
    public function editListing(
        ListingFileRepository         $listingFileRepository,
        ListingRepository             $listingRepository,
        Request                       $request,
        AuthorizationCheckerInterface $authChecker,
        AppServices                   $services,
        EventService                  $eventService,
                                      $slug = null,
        CategoryRepository            $categoryRepository
    )
    {
        $organizer = $this->getUser()->getOrganizer();
        $organizerCategory = $organizer->getCategory();

        $listing = $this->initializeListing($slug);

        if ($listing == null) {
            return $services->redirectToReferer('listing');
        }

        $userEvents = $eventService->getEvents(['organizer' => $this->getUser()->getOrganizer()->getSlug()]);
        $entityManager = $this->getDoctrine()->getManager();
        $formgeneral = $this->createForm(ListingType::class, $listing, [
            'validation_groups' => ['create', 'Default'],
        ]);

        $em = $this->getDoctrine()->getManager();
        $formgeneral->handleRequest($request);

        if ($formgeneral->isSubmitted() && $formgeneral->isValid()) {
            $this->processSubmittedListing($listing, $em, $services);

            if (!$slug) {
                $this->addFlash('success', 'The listing has been successfully created');
            } else {
                $this->addFlash('success', 'The listing has been successfully updated');
            }

            return $this->redirectToRouteAfterSubmission($authChecker);
        }

        $formspecific = $this->createForm(ListingTypeSpecific::class, $listing, [
            'validation_groups' => ['create', 'Default'],
            'user_events' => $userEvents,
        ]);

        $em = $this->getDoctrine()->getManager();
        $formspecific->handleRequest($request);

        if ($formspecific->isSubmitted() && $formspecific->isValid()) {
            $this->processSubmittedListing($listing, $em, $services);

            if (!$slug) {
                $this->addFlash('success', 'The listing has been successfully created');
            } else {
                $this->addFlash('success', 'The listing has been successfully updated');
            }

            return $this->redirectToRouteAfterSubmission($authChecker);
        }

        return $this->renderListingForm($listing, $formgeneral, $formspecific);
    }

    /**
     * @Route("/dashboard/attendee/subcance/{id}/subcancel", name="dashboard_attendee_cancel", methods="GET")
     * @throws Exception
     */
    public function cancel(AppServices $services, ListingSubmissionService $listingSubmissionService, OrderRepository $orderRepository, $id)
    {
        $listingsub = $listingSubmissionService->getSubmissionsById($id)[0];
        if (!$listingsub) {
            $this->addFlash('error', 'Cannot be found');
            return $services->redirectToReferer('listingsub');
        }

        if ($listingsub->getDeletedAt() !== null) {
            $this->addFlash('error', 'Your application in this listing has been canceled');
//            } else {
//                $this->addFlash('success', 'The listing has been deleted');
        }

        $em = $this->getDoctrine()->getManager();
        $listingsub->setDeletedAt(new DateTime());
        $em->persist($listingsub);
        $em->flush();
        return $services->redirectToReferer('listingsub');
    }

    /**
     * @Route("/dashboard/organizer/my-listings/{slug}/delete-permanently", name="dashboard_organizer_listing_delete_permanently", methods="GET")
     * @Route("/dashboard/organizer/my-listings/{slug}/delete", name="dashboard_organizer_listing_delete", methods="GET")
     * @throws Exception
     */
    public function delete(AppServices $services, ListingService $listingService, OrderRepository $orderRepository, $slug)
    {
        $organizer = "all";

        $listing = $listingService->getListings(array("slug" => $slug, "published" => "all", "elapsed" => "all", "organizer" => $organizer, "organizerEnabled" => "all"))[0];
        if (!$listing) {
            $this->addFlash('error', 'The listing can not be found');
            return $services->redirectToReferer('listing');
        }

        if ($listing->getDeletedAt() !== null) {
            $this->addFlash('error', 'The listing has been deleted permanently');
        } else {
            $this->addFlash('success', 'The listing has been deleted');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($listing);
        $em->flush();
        return $services->redirectToReferer('listing');
    }

    /**
     * @Route("/dashboard/organizer/manage-listings/{slug}/restore", name="dashboard_organizer_listing_restore", methods="GET")
     * @throws InvalidDatabaseException|NonUniqueResultException
     */
    public function restore($slug, ListingService $listingService)
    {
        $listing = $services->getListings(array("slug" => $slug, "published" => "all", "elapsed" => "all", "organizer" => "all", "organizerEnabled" => "all"))[0];
        if (!$listing) {
            $this->addFlash('error', 'The listing can not be found');
            return $services->redirectToReferer('listing');
        }
        $listing->setDeletedAt(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($listing);
        $em->flush();
        $this->addFlash('success', 'The listing has been succesfully restored');

        return $services->redirectToReferer('listing');
    }

    /**
     * @Route("/dashboard/organizer/my-listings/{slug}/publish", name="dashboard_organizer_listing_publish", methods="GET")
     * @Route("/dashboard/organizer/my-listings/{slug}/draft", name="dashboard_organizer_listing_draft", methods="GET")
     * @throws InvalidDatabaseException|NonUniqueResultException|Exception|InvalidArgumentException|\Psr\Cache\InvalidArgumentException
     */
    public function showhide(ListingService $listingService, AppServices $services, $slug, Swift_Mailer $mailer, Twig_Environment $templating)
    {
        $organizer = $this->getUser()->getOrganizer()->getSlug();
        $listing = $listingService->getListings(array("slug" => $slug, "organizer" => $organizer))[0];
        if (!$listing) {
            $this->addFlash('error', 'The listing can not be found');
            return $services->redirectToReferer('listing');
        }
        if ($listing->isPublished() === true) {
            $listing->setPublished(false);
            $this->addFlash('success', 'The listing has been unpublished and will not be included in the search results');
        } else {
            $listing->setPublished(true);
            $this->addFlash('success', 'The listing has been published and will show up in the search results');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($listing);
        $em->flush();
        return $services->redirectToReferer('listing');
    }

    /**
     * @Route("/dashboard/organizer/my-listings/{slug}/details", name="dashboard_organizer_listing_details", methods="GET", condition="request.isXmlHttpRequest()")
     * @throws InvalidDatabaseException|NonUniqueResultException
     */
    public function details(ListingService $listingService, $slug, AuthorizationCheckerInterface $authChecker)
    {
        $organizer = "all";
        if ($authChecker->isGranted('ROLE_ORGANIZER')) {
            $organizer = $this->getUser()->getOrganizer()->getSlug();
        }

        $listing = $listingService->getListings(array("slug" => $slug, "published" => "all", "elapsed" => "all", "organizer" => $organizer, "organizerEnabled" => "all"))[0];
        if (!$listing) {
            return new Response('The listing can not be found');
        }
        return $this->render('Dashboard/Shared/Listing/details.html.twig', [
            'listing' => $listing,
        ]);
    }

    public function downloadFileAction($id, ListingSubmissionService $listingSubmissionService)
    {
        $listingsub = $listingSubmissionService->getSubmissionsById($id)[0];

        if (!$listingsub) {
            throw $this->createNotFoundException('Listing submission not found');
        }

        // Check if the file exists
        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/files/' . $listingsub->getFileName();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        // Create a BinaryFileResponse to handle the file download
        $response = new BinaryFileResponse($filePath);

        // Set the file name in the response headers
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $listingsub->getFileOriginalName());

        return $response;
    }

    /**
     * @Route("/dashboard/attendee/my-listingsubs/{id}/details", name="dashboard_listing_details", methods="GET")
     */
    public function detailsattendee(ListingService $listingService, $id, AuthorizationCheckerInterface $authChecker, ListingSubmissionService $listingSubmissionService)
    {
        $listingsub = $listingSubmissionService->getSubmissionsById($id)[0];
        if (!$listingsub) {
            return new Response('The application can not be found');
        }
        return $this->render('Dashboard/Shared/ListingSubmissions/details.html.twig', [
            'listingsub' => $listingsub,
        ]);
    }

    private function getCategoryByName($categoryName, $categoryRepository)
    {
        return $categoryRepository->findByName($categoryName);
    }
}