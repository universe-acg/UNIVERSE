<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Entity\AthleticBooking;
use App\Form\AthleticBookingType;
use App\Service\AppServices;
use App\Service\AthleticBookingService;
use App\Service\AthleticService;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AthleticsController extends AbstractController
{

    private $athleticService;

    public function __construct(AthleticService $athleticService)
    {
        $this->athleticService = $athleticService;
    }

    /**
     * @Route("/athletics", name="athletics")
     */
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services, AthleticService $athleticService): Response
    {
        $athleticfacilities = $paginator->paginate($athleticService->getAthletics([]), $request->query->getInt('page', 1), 25);
        return $this->render('Front/Athletics/athletics.html.twig', [
            'athletics' => $athleticfacilities
        ]);
    }

    /**
     * @Route("/athleticfacility/{slug}", name="athletic_details")
     */
    public function athleticBySlug($slug, AppServices $services, AthleticService $athleticService, PaginatorInterface $paginator, Request $request, AthleticBookingService $athleticBookingService): Response
    {

        $em = $this->getDoctrine()->getManager();
        $facility = $this->athleticService->getAthleticBySlug($slug);

        $monthlySchedules = $facility->getMonthlySchedule();

        $data = [];

        foreach ($facility->getMonthlySchedule() as $monthlySchedule) {
            $startDate = $monthlySchedule->getStartDate();
            $endDate = $monthlySchedule->getEndDate();

            // Calculate the list of days between startDate and endDate
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dayData = [
                    'dayInLetters' => $currentDate->format('l'),
                    'dateOfDay' => $currentDate->format('Y-m-d'),
                    'durations' => [],
                ];

                // For each day, calculate the list of durations and start times
                foreach ($monthlySchedule->getOperatingHours() as $operatingHour) {
                    $startTime = $operatingHour->getStartTime();
                    $endTime = $operatingHour->getEndTime();
                    $duration = $facility->getDuration(); // You might want to adjust this based on your business logic

                    // Check if the operating hour is for the current day
                    if ($currentDate->format('l') == $operatingHour->getDayOfWeek()) {
                        // Calculate the list of start times in 5-minute intervals
                        $startTimes = [];
                        $currentTime = clone $startTime;

                        while ($currentTime <= $endTime) {
                            $endDateTime = clone $currentTime;
                            $endDateTime->modify('+' . $duration . ' minutes');

                            // Check if adding the duration doesn't go past the endTime
                            if ($endDateTime <= $endTime) {
                                $startTimes[] = $currentTime->format('H:i');
                            }

                            $currentTime->modify('+5 minutes');
                        }

                        // Add the duration data only for the specific day
                        $dayData['durations'][] = [
                            'durationInLetters' => $duration . ' minutes',
                            'duration' => $duration,
                            'startTimes' => $startTimes,
                        ];
                    }
                }

                // Only add the dayData if there are durations
                if (!empty($dayData['durations'])) {
                    $data[] = $dayData;
                }

                $currentDate->modify('+1 day');
            }
        }
        // Check if the venue exists
        if (!$facility) {
            throw $this->createNotFoundException('Athletic facility not found');
        }
        $reservations = $athleticBookingService->getAthleticBookingById($facility->getId());

        $currentDateTime = new DateTime();

        foreach ($reservations as $reservation) {
            $bookingDate = $reservation->getBookingDate();

            // Compare booking date with current date and time
            $isPast = $bookingDate < $currentDateTime;

            // Set the $isPast property in your Reservation entity
            $reservation->setIsPast($isPast);
        }

        usort($reservations, function ($a, $b) {
            $dateA = $a->getBookingDate();
            $dateB = $b->getBookingDate();

            // Compare dates for sorting
            if ($dateA > $dateB) {
                return -1; // $a comes before $b
            } elseif ($dateA < $dateB) {
                return 1; // $b comes before $a
            } else {
                return 0; // dates are equal
            }
        });

// Now $reservations is sorted by booking date

        $newathlteicbooking = new AthleticBooking();
        $form = $this->createForm(AthleticBookingType::class, $newathlteicbooking);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $newathlteicbooking->setFacility($facility);
                $newathlteicbooking->setUser($this->getUser());

                $bookingDate = new DateTime(
                    $form->get('bookingDate')->getData()->format('Y-m-d') . ' ' .
                    (new DateTime($form->get('bookingTime')->getData()))->format('H:i')
                );

                $formattedDate = new DateTime($bookingDate->format('Y-m-d H:i:s'));
                $newathlteicbooking->setBookingDate($formattedDate);
                $em->persist($newathlteicbooking);
                $em->flush();

                if (!$slug) {
                    $this->addFlash('success', 'The athletic facility has been successfully created');
                } else {
                    $this->addFlash('success', 'The athletic facility has been successfully updated');
                }

                return $this->redirectToRoute("athletic_details", ['slug' => $slug], Response::HTTP_FOUND);
            }

            $this->addFlash('error', 'The form contains invalid data');
        }

        // Assuming getSecondaryImages and getImagePath return arrays of image paths
        $secondaryImages = $facility->getSecondaryImages()->toArray();
        $imagePath = [$facility->getImagePath()];

        // Extract only the getImagePath from each element in $secondaryImages
        $secondaryImagePaths = [];
        foreach ($secondaryImages as $secondaryImage) {
            $secondaryImagePaths[] = $secondaryImage->getImagePath();
        }
// Merge the two arrays into one
        $allImagePaths = array_merge($secondaryImagePaths, $imagePath);

        return $this->render('Front/Athletics/facility_details.html.twig', [
            'facility' => $facility,
            'services' => $services,
            'reservations' => $reservations,
            'allImagePaths' => $allImagePaths,
            'formbook' => $form->createView(),
            'data' => json_encode($data)
        ]);
    }
}
