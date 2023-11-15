<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Repository\ListingFileRepository;
use App\Repository\ListingRepository;
use App\Repository\OrderRepository;
use App\Service\AppServices;
use App\Service\ListingService;
use InvalidArgumentException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

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
    public function listing($slug, OrderRepository $orderRepository, ListingService $listingService, PaginatorInterface $paginator, Request $request, AppServices $services): Response
    {
        $listing = $listingService->getListingBySlug($slug);

        if (!$listing) {
            $this->addFlash('error', 'The listing could not be found');
            return $this->redirectToRoute('listings');
        }

        $listing->viewed();
        $em = $this->getDoctrine()->getManager();
        $em->persist($listing);
        $em->flush();

        // check if the user has already booked a ticket
        $user = $this->getUser();

//        $orders = $orderRepository->getOrdersByUser($user);
//        $listingDates = [];
//
//        Iterate through the user's orders and order elements
//        foreach ($orders as $order) {
//            $orderElements = $order->getOrderelements();
//
//            foreach ($orderElements as $orderElement) {
//                // Get the listing date associated with the order element
//                $listingDate = $orderElement->getListingticket()->getListingdate();
//
//                // Add the listing date to the array if it's not already included
//                if (!in_array($listingDate, $listingDates, true)) {
//                    $listingDates[] = $listingDate;
//                }
//            }
//        }
//
//        foreach ($listingDates as $listingDate) {
//            if ($listingDate->getTickets()[0]->getActive()) {
//                $listingDate->getTickets()[0]->setAlreadybooked(true);
//            }
//        }

        return $this->render('Front/Listing/listing.html.twig', ["listing" => $listing]);
    }
}
