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
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\HttpFoundation\BinaryFileResponse;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
    
    class ListingController extends AbstractController
    {
        /**
         * @Route("/listings", name="listings")
         * @throws InvalidArgumentException
         */
        public function listings(Request            $request, ListingService $listingService, ListingRepository $listingRepository,
                                 PaginatorInterface $paginator, AppServices $services): Response
        {
            
            $keyword = ($request->query->get('keyword')) == "" ? "all" : $request->query->get('keyword');
            $originalListings = $listingService->getListings(array('keyword' => $keyword));

// Duplicate the listings 20 times
            $duplicatedListings = array_fill(0, 20, $originalListings);

// Merge the duplicated listings into a single array
            $resultListings = array_merge(...$duplicatedListings);
            
            $listings = $paginator->paginate($resultListings, $request->query->getInt('page', 1), 42);
            
            return $this->render('Front/Listing/listings.html.twig', [
                'listings' => $listings
            ]);
        }
        
        /**
         * @Route("/listing/management/files/download/{fileId}", name="dashboard_organizer_management_files_download_file_listing")
         */
        public function download($fileId, ListingFileRepository $listingFileRepository, ParameterBagInterface $parameterBag)
        {
            // Retrieve the File entity based on $fileId
            $file = $listingFileRepository->find($fileId);
            
            // Check if the file exists
            if (!$file) {
                throw $this->createNotFoundException('File not found');
            }
            // Get the absolute file path
            $filePath = $parameterBag->get('kernel.project_dir') . "/public/uploads/files/" . $file->getFileName();
            
            // Create a BinaryFileResponse for downloading the file
            $response = new BinaryFileResponse($filePath);
            
            // Set the file name for the download
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file->getFileName() // Replace with your actual method to get the file name
            );
            
            $response->headers->set('Content-Disposition', $disposition);
            
            return $response;
        }
        
        /**
         * @Route("/listing/{slug}", name="listing")
         */
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
            
            if ($form->isSubmitted() && $form->isValid()) {
                $listingsubmissionexists = $listingSubmissionService->checkIfExists($listing, $this->getUser());
                $listingsubmission->setUser($this->getUser());
                if ($listingsubmissionexists) {
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
         * @Route("/applylisting/{slug}/{userid}", name="applylisting")
         */
        public function applylisting(Request $request, ListingService $listingService, AppServices $services,
                                             $slug, $userid)
        {
            try {
                // Handle regular POST request logic here
                // Use $request->request->get('parameter_name') to retrieve POST data
                // Example: $postData = $request->request->get('data');
                
                // Perform necessary operations and check for errors
                // ...
                
                // If everything is successful, return a JSON response with 200 status code
                $responseData = [
                    'status' => 'success',
                    'message' => 'Application submitted successfully!',
                ];
                
                return new JsonResponse($responseData, Response::HTTP_OK);
            } catch (Exception $e) {
                // Handle errors and return a JSON response with 400 status code
                $responseData = [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
                
                return new JsonResponse($responseData, Response::HTTP_BAD_REQUEST);
            }
        }
    }
