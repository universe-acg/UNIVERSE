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
use App\Service\DemoMailer as Swift_Mailer;
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use \Twig\Environment;

class ListingController extends Controller
{
    public function apply(Request $request, string $slug, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$this->isCsrfTokenValid('action', $request->request->get('_token') ?? $request->headers->get('X-CSRF-Token'))) throw $this->createAccessDeniedException('Invalid action token.');
        $listing=$em->getRepository(Listing::class)->findOneBy(['slug'=>$slug,'published'=>true]);
        if (!$listing) throw $this->createNotFoundException();
        if ($em->getRepository(\App\Entity\ListingSubmission::class)->findOneBy(['listing'=>$listing,'user'=>$this->getUser()])) return $this->json(['error'=>'An application already exists.'],409);
        $position=$listing->getDatePositions()->first();
        if (!$position) return $this->json(['error'=>'No application position is available.'],422);
        $content=trim((string)$request->request->get('content',''));
        if (mb_strlen($content)<20 || mb_strlen($content)>5000) return $this->json(['error'=>'Enter between 20 and 5000 characters.'],422);
        $submission=new \App\Entity\ListingSubmission();$submission->setListing($listing);$submission->setPosition($position);$submission->setUser($this->getUser());$submission->setContent($content);
        $em->persist($submission);$em->flush();
        return $this->json(['message'=>'Application received.'],201);
    }
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
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ListingService $listingService
     * @return Response
     */
#[Route("/organizer/manage-listings", name: "dashboard_organizer_listing", methods: ["GET"])]
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
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ListingService $listingService
     * @return Response
     */
#[Route("/attendee/my-listing-applications", name: "mylistingapplicaitons", methods: ["GET"])]
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
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ListingService $listingService
     * @param ListingSubmissionService $listingSubmissionService
     * @return Response
     */
#[Route("/organizer/my-listing-applications-creator/{id}", name: "mylistingapplicaitonscreator", methods: ["GET"])]
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
     *
     * @throws Exception|InvalidArgumentException
     */
#[Route("/organizer/my-listings/add", name: "dashboard_organizer_listing_add", methods: ["GET","POST"])]
    public function addListing(Request                       $request, AppServices                   $services, ListingService                $listingService, EventService                  $eventService, AuthorizationCheckerInterface $authChecker, $slug = null)
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

        $baseSlug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));

        $randomString = bin2hex(random_bytes(10)); // Adjust the number of bytes for desired length

        $uniqueSlug = $baseSlug . '-' . $randomString;

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Listing::class);

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






    }


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
     *
     * @throws InvalidDatabaseException
     * @throws Exception
     */
#[Route("/organizer/my-listings/{slug}/edit", name: "dashboard_organizer_listing_edit", methods: ["GET","POST"])]
    public function editListing(ListingFileRepository         $listingFileRepository, ListingRepository             $listingRepository, Request                       $request, AuthorizationCheckerInterface $authChecker, AppServices                   $services, EventService                  $eventService, CategoryRepository            $categoryRepository, $slug = null)
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
     *
     * @throws Exception
     */
#[Route("/attendee/subcance/{id}/subcancel", name: "dashboard_attendee_cancel", methods: ["POST"])]
    public function cancel(AppServices $services, ListingSubmissionService $listingSubmissionService, OrderRepository $orderRepository, $id)
    {
        $listingsub = ($listingSubmissionService->getSubmissionsById($id)[0] ?? null);
        if (!$listingsub) {
            $this->addFlash('error', 'Cannot be found');
            return $services->redirectToReferer('listingsub');
        }

        if ($listingsub->getDeletedAt() !== null) {
            $this->addFlash('error', 'Your application in this listing has been canceled');


        }

        $em = $this->getDoctrine()->getManager();
        $listingsub->setDeletedAt(new DateTime());
        $em->persist($listingsub);
        $em->flush();
        return $services->redirectToReferer('listingsub');
    }

    /**
     *
     *
     * @throws Exception
     */
#[Route("/organizer/my-listings/{slug}/delete-permanently", name: "dashboard_organizer_listing_delete_permanently", methods: ["POST"])]
#[Route("/organizer/my-listings/{slug}/delete", name: "dashboard_organizer_listing_delete", methods: ["POST"])]
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
     *
     * @throws InvalidDatabaseException|NonUniqueResultException
     */
#[Route("/organizer/manage-listings/{slug}/restore", name: "dashboard_organizer_listing_restore", methods: ["POST"])]
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
     *
     *
     * @throws InvalidDatabaseException|NonUniqueResultException|Exception|InvalidArgumentException|\Psr\Cache\InvalidArgumentException
     */
#[Route("/organizer/my-listings/{slug}/publish", name: "dashboard_organizer_listing_publish", methods: ["POST"])]
#[Route("/organizer/my-listings/{slug}/draft", name: "dashboard_organizer_listing_draft", methods: ["POST"])]
    public function showhide(ListingService $listingService, AppServices $services, $slug, Swift_Mailer $mailer, \Twig\Environment $templating)
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
     *
     * @throws InvalidDatabaseException|NonUniqueResultException
     */
#[Route("/organizer/my-listings/{slug}/details", name: "dashboard_organizer_listing_details", methods: ["GET"], condition: "request.isXmlHttpRequest()")]
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
        $listingsub = ($listingSubmissionService->getSubmissionsById($id)[0] ?? null);

        if (!$listingsub) {
            throw $this->createNotFoundException('Listing submission not found');
        }

        $this->denyAccessUnlessGranted('ROLE_USER');
        $owner=$listingsub->getUser();$organizer=$listingsub->getListing()->getOrganizer()->getUser();
        if (!$this->isGranted('ROLE_ADMINISTRATOR') && $this->getUser()->getId() !== $owner->getId() && $this->getUser()->getId() !== $organizer->getId()) throw $this->createAccessDeniedException();
        $filePath = $this->getParameter('kernel.project_dir') . '/var/private/applications/' . basename($listingsub->getFileName() ?? '');

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        $response = new BinaryFileResponse($filePath);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $listingsub->getFileOriginalName());

        return $response;
    }

    /**
     *
     */
#[Route("/attendee/my-listingsubs/{id}/details", name: "dashboard_listing_details", methods: ["GET"])]
    public function detailsattendee(ListingService $listingService, $id, AuthorizationCheckerInterface $authChecker, ListingSubmissionService $listingSubmissionService)
    {
        $listingsub = ($listingSubmissionService->getSubmissionsById($id)[0] ?? null);
        if (!$listingsub) {
            return new Response('The application can not be found');
        }
        if (!$this->isGranted('ROLE_ADMINISTRATOR') && $listingsub->getUser()->getId() !== $this->getUser()->getId() && $listingsub->getListing()->getOrganizer()->getUser()->getId() !== $this->getUser()->getId()) throw $this->createAccessDeniedException();
        return $this->render('Dashboard/Shared/ListingSubmissions/details.html.twig', [
            'listingsub' => $listingsub,
        ]);
    }

    private function getCategoryByName($categoryName, $categoryRepository)
    {
        return $categoryRepository->findByName($categoryName);
    }
}
