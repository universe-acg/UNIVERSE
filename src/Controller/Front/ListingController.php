<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Controller\Front;

    use App\Entity\ListingSubmission;
    use App\Form\ListingSubmissionType;
    use App\Repository\ListingFileRepository;
    use App\Repository\ListingRepository;
    use App\Repository\OrderRepository;
    use App\Service\AppServices;
    use App\Service\ListingService;
    use App\Service\ListingSubmissionService;
    use Exception;
    use InvalidArgumentException;
    use Knp\Component\Pager\PaginatorInterface;
    use App\Controller\BaseController as AbstractController;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\HttpFoundation\BinaryFileResponse;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

    class ListingController extends AbstractController
    {
        /**
         *
         * @throws InvalidArgumentException
         */
#[Route("/listings", name: "listings")]
        public function listings(Request            $request, ListingService $listingService, ListingRepository $listingRepository,
                                 PaginatorInterface $paginator, AppServices $services): Response
        {

            $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
            $originalListings = $listingService->getListings(array('keyword' => $keyword));

            $duplicatedListings = array_fill(0, 20, $originalListings);

            $resultListings = array_merge(...$duplicatedListings);

            $listings = $paginator->paginate($resultListings, $request->query->getInt('page', 1), 42);

            return $this->render('Front/Listing/listings.html.twig', [
                'listings' => $listings
            ]);
        }

        /**
         *
         */
#[Route("/listing/management/files/download/{fileId}", name: "dashboard_organizer_management_files_download_file_listing")]
        public function download($fileId, ListingFileRepository $listingFileRepository, ParameterBagInterface $parameterBag)
        {

            $file = $listingFileRepository->find($fileId);

            if (!$file) {
                throw $this->createNotFoundException('File not found');
            }

            $filePath = $parameterBag->get('kernel.project_dir') . "/public/uploads/files/" . $file->getFileName();

            $response = new BinaryFileResponse($filePath);

            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file->getFileName() // Replace with your actual method to get the file name
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        /**
         *
         */
#[Route("/listing/{slug}", name: "listing")]
        public function listing($slug,
                                ListingSubmissionService $listingSubmissionService,
                                OrderRepository $orderRepository,
                                AuthorizationCheckerInterface $authChecker,
                                ListingService $listingService, PaginatorInterface $paginator, Request $request, AppServices $services): Response
        {
            $listing = $listingService->getListingBySlug($slug);

            if (!$listing) {
                $this->addFlash('error', 'The listing could not be found');
                return $this->redirectToRoute('listings');
            }

            $listing->viewed();
            $em = $this->getDoctrine()->getManager();

            $listingsubmission = $this->initializeListingSubmission();
            $listingsubmission->setListing($listing);
            $formtype = ListingSubmissionType::class;
            $form = $this->createForm($formtype, $listingsubmission, [
                'validation_groups' => ['create', 'Default'],
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted()) $this->denyAccessUnlessGranted('ROLE_ATTENDEE');
            if ($form->isSubmitted() && $form->isValid()) {
                $listingsubmissionexists = $listingSubmissionService->checkIfExists($listing, $this->getUser());
                $listingsubmission->setUser($this->getUser());
                if (!$listingsubmissionexists) {
                    $em->persist($listingsubmission);
                    $this->addFlash('success', 'You have already applied here before. New application submitted');
                } else {
                    $em->persist($listingsubmission);
                    $this->addFlash('success', 'Successfully submitted!');
                }
                $em->flush();
                return $this->redirectToRouteAfterSubmission($authChecker, $listing->getSlug());
            }

            return $this->render('Front/Listing/listing.html.twig', [
                "listing" => $listing,
                "form" => $form->createView(),
            ]);
        }

        private function initializeListingSubmission()
        {
            return new ListingSubmission();
        }

        private function redirectToRouteAfterSubmission($authChecker, $slug)
        {
            return $this->redirectToRoute("listing", ['slug' => $slug]);
        }

        /**
         *
         */
#[Route("/applylisting/{slug}/{userid}", name: "applylisting", methods: ["POST"])]
        public function applylisting(Request $request, ListingService $listingService, AppServices $services,
                                             $slug, $userid)
        {
            $this->denyAccessUnlessGranted('ROLE_ATTENDEE');
            if ((int)$userid !== $this->getUser()->getId()) throw $this->createAccessDeniedException();
            $listing = $listingService->getListingBySlug($slug);
            if (!$listing || !$listing->getPublished()) throw $this->createNotFoundException();
            $em = $this->getDoctrine()->getManager();
            if ($em->getRepository(ListingSubmission::class)->findOneBy(['listing'=>$listing,'user'=>$this->getUser()])) return $this->json(['error'=>'You already applied.'],409);
            $submission = new ListingSubmission();$submission->setListing($listing);$submission->setUser($this->getUser());$submission->setContent(trim((string)$request->request->get('content','I would like to take part in this opportunity.')));
            $em->persist($submission);$em->flush();return $this->json(['status'=>'success','message'=>'Application submitted.'],201);

        }
    }
