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
use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AthleticsController extends AbstractController
{

    private $athleticService;

    public function __construct(AthleticService $athleticService)
    {
        $this->athleticService = $athleticService;
    }

    /**
     *
     */
#[Route("/athletics", name: "athletics")]
    public function index(Request $request, PaginatorInterface $paginator, AppServices $services, AthleticService $athleticService, \App\Service\PublicCatalogue $catalogue): Response
    {
        $athleticfacilities = $paginator->paginate($catalogue->filter($athleticService->getAthletics([]), $request->query->getString('keyword'), $request->query->getString('sortedby', 'created_desc')), max(1, $request->query->getInt('page', 1)), 24);
        return $this->render('Front/Athletics/athletics.html.twig', [
            'athletics' => $athleticfacilities
        ]);
    }

    /**
     *
     */
#[Route("/athleticfacility/{slug}", name: "athletic_details")]
    public function athleticBySlug($slug, AppServices $services, AthleticService $athleticService, PaginatorInterface $paginator, Request $request, AthleticBookingService $athleticBookingService): Response
    {

        $em = $this->getDoctrine()->getManager();
        $facility = $this->athleticService->getAthleticBySlug($slug);

        if (!$facility) throw $this->createNotFoundException('Athletic facility not found');
        $monthlySchedules = $facility->getMonthlySchedule();

        $data = [];

        foreach ($facility->getMonthlySchedule() as $monthlySchedule) {
            $startDate = $monthlySchedule->getStartDate();
            $endDate = $monthlySchedule->getEndDate();

            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dayData = [
                    'dayInLetters' => $currentDate->format('l'),
                    'dateOfDay' => $currentDate->format('Y-m-d'),
                    'durations' => [],
                ];

                foreach ($monthlySchedule->getOperatingHours() as $operatingHour) {
                    $startTime = $operatingHour->getStartTime();
                    $endTime = $operatingHour->getEndTime();
                    $duration = $facility->getDuration(); // You might want to adjust this based on your business logic

                    if ($currentDate->format('l') == $operatingHour->getDayOfWeek()) {

                        $startTimes = [];
                        $currentTime = clone $startTime;

                        while ($currentTime <= $endTime) {
                            $endDateTime = clone $currentTime;
                            $endDateTime->modify('+' . $duration . ' minutes');

                            if ($endDateTime <= $endTime) {
                                $startTimes[] = $currentTime->format('H:i');
                            }

                            $currentTime->modify('+5 minutes');
                        }

                        $dayData['durations'][] = [
                            'durationInLetters' => $duration . ' minutes',
                            'duration' => $duration,
                            'startTimes' => $startTimes,
                        ];
                    }
                }

                if (!empty($dayData['durations'])) {
                    $data[] = $dayData;
                }

                $currentDate->modify('+1 day');
            }
        }

        if (!$facility) {
            throw $this->createNotFoundException('Athletic facility not found');
        }
        $reservations = $athleticBookingService->getAthleticBookingByFacility($facility);

        $currentDateTime = new DateTime();

        foreach ($reservations as $reservation) {
            $bookingDate = $reservation->getBookingDate();

            $isPast = $bookingDate < $currentDateTime;

            $reservation->setIsPast($isPast);
        }

        usort($reservations, function ($a, $b) {
            $dateA = $a->getBookingDate();
            $dateB = $b->getBookingDate();

            if ($dateA > $dateB) {
                return -1; // $a comes before $b
            } elseif ($dateA < $dateB) {
                return 1; // $b comes before $a
            } else {
                return 0; // dates are equal
            }
        });


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
                try { $athleticBookingService->reserve($newathlteicbooking); }
                catch (\InvalidArgumentException $error) { $this->addFlash('error',$error->getMessage()); return $this->redirectToRoute('athletic_details',['slug'=>$slug]); }

                if (!$slug) {
                    $this->addFlash('success', 'The athletic facility has been successfully created');
                } else {
                    $this->addFlash('success', 'Your reservation has been saved in the demo portal');
                }

                return $this->redirectToRoute("athletic_details", ['slug' => $slug], Response::HTTP_FOUND);
            }

            $this->addFlash('error', 'The form contains invalid data');
        }

        $secondaryImages = $facility->getSecondaryImages()->toArray();
        $imagePath = [$facility->getImagePath()];

        $secondaryImagePaths = [];
        foreach ($secondaryImages as $secondaryImage) {
            $secondaryImagePaths[] = $secondaryImage->getImagePath();
        }

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
