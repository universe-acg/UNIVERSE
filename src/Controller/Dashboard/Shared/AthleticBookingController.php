<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Service\AthleticBookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AthleticBookingController extends AbstractController
{
    private $bookingService;

    public function __construct(AthleticBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @Route("/book/{facilityId}/{scheduleId}/{bookingDate}/{duration}", name="book_reservation")
     */
    public function bookReservation($facilityId, $scheduleId, $bookingDate, $duration): Response
    {
        // You can add code here to handle the reservation creation
        $user = $this->getUser(); // Get the currently logged-in user

        $athleticBooking = $this->bookingService->createAthleticBooking($facilityId, $user->getId(), $scheduleId, $bookingDate, $duration);

        // You can add code to handle success or failure (e.g., render a view, return a JSON response)
        // For example, return a confirmation message or error message
        return $this->render('booking/book.html.twig', ['athleticBooking' => $athleticBooking]);
    }

    /**
     * @Route("/cancel/{athleticBookingId}", name="cancel_reservation")
     */
    public function cancelReservation($athleticBookingId): Response
    {
        // You can add code here to handle reservation cancellation
        $this->bookingService->cancelAthleticBooking($athleticBookingId);

        // You can add code to handle success or failure (e.g., render a view, return a JSON response)
        // For example, return a confirmation message or error message
        return $this->redirectToRoute('my_bookings');
    }

    /**
     * @Route("/delete/{athleticBookingId}", name="delete_reservation")
     */
    public function deleteReservation($athleticBookingId): Response
    {
        // You can add code here to handle reservation deletion
        $this->bookingService->deleteAthleticBooking($athleticBookingId);

        // You can add code to handle success or failure (e.g., render a view, return a JSON response)
        // For example, return a confirmation message or error message
        return $this->redirectToRoute('my_bookings');
    }
}