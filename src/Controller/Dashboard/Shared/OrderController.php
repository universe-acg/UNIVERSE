<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Repository\EventDateRepository;
use App\Repository\EventRepository;
use App\Repository\EventTicketRepository;
use App\Repository\OrderElementRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\AppServices;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class OrderController extends Controller
{

    /**
     *
     *
     *
     */
#[Route("/attendee/my-tickets", name: "dashboard_attendee_orders")]
#[Route("/organizer/manage-orders", name: "dashboard_organizer_orders")]
#[Route("/administrator/manage-orders", name: "dashboard_administrator_orders")]
    public function orders(Request $request, AppServices $services, PaginatorInterface $paginator, AuthorizationCheckerInterface $authChecker)
    {

        $reference = ($request->query->get('reference')) == "" ? "all" : $request->query->get('reference');
        $event = ($request->query->get('event')) == "" ? "all" : $request->query->get('event');
        $eventdate = ($request->query->get('eventdate')) == "" ? "all" : $request->query->get('eventdate');
        $eventticket = ($request->query->get('eventticket')) == "" ? "all" : $request->query->get('eventticket');
        $user = ($request->query->get('user')) == "" ? "all" : $request->query->get('user');
        $organizer = ($request->query->get('organizer')) == "" ? "all" : $request->query->get('organizer');
        $datefrom = ($request->query->get('datefrom')) == "" ? "all" : $request->query->get('datefrom');
        $dateto = ($request->query->get('dateto')) == "" ? "all" : $request->query->get('dateto');
        $status = ($request->query->get('status')) == "" ? "all" : $request->query->get('status');

        $authChecker->isGranted("ROLE_ATTENDEE") ? $status = "all" : $upcomingtickets = "all";

        $ordersQuery = $services->getOrders(array("reference" => $reference, "event" => $event, "eventdate" => $eventdate, "eventticket" => $eventticket, "user" => $user, "organizer" => $organizer, "datefrom" => $datefrom, "dateto" => $dateto, "status" => $status))->getQuery();

        if (($authChecker->isGranted("ROLE_ADMINISTRATOR") || $authChecker->isGranted("ROLE_ORGANIZER")) && (($request->query->get('excel') == "1" || $request->query->get('csv') == "1" || $request->query->get('pdf') == "1"))) {
            $orders = $ordersQuery->getResult();
            if (!count($orders)) {
                $this->addFlash('error', 'No orders found to be included in the report');
                return $services->redirectToReferer("orders");
            }
            if ($request->query->get('pdf') == "1") {
                if (!$request->query->get('event')) {
                    $this->addFlash('error', 'You must choose an event in order to export the attendees list');
                    return $services->redirectToReferer("orders");
                }
                if ($request->query->get('status') != "1") {
                    $this->addFlash('error', 'You must set the status to paid in order to export the attendees list');
                    return $services->redirectToReferer("orders");
                }
                $organizer = "all";
                if ($authChecker->isGranted('ROLE_ORGANIZER')) {
                    $organizer = $this->getUser()->getOrganizer()->getSlug();
                }
                $event = $services->getEvents(array("slug" => $request->query->get('event'), "published" => "all", "elapsed" => "all", "organizer" => $organizer))[0];
                if (!$event) {
                    $this->addFlash('error', 'The event can not be found');
                    return $services->redirectToReferer("orders");
                }
                $pdfOptions = new Options();

                $dompdf = new Dompdf($pdfOptions);
                $html = $this->renderView('Dashboard/Shared/Order/attendees-pdf.html.twig', [
                    'event' => $event,
                    'orders' => $orders
                ]);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $dompdf->stream($event->getName() . ": " . "Attendees list", [
                    "Attachment" => false
                ]);
            }
        }

        $ordersPagination = $paginator->paginate($ordersQuery, $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        return $this->render('Dashboard/Shared/Order/orders.html.twig', [
            'orders' => $ordersPagination,
        ]);
    }

    /**
     *
     */
#[Route("/organizer/manage-attendees", name: "dashboard_organizer_attendees")]
    public function attendees(OrderRepository $orderRepository, Request $request, AppServices $services, PaginatorInterface $paginator, AuthorizationCheckerInterface $authChecker, EventRepository $eventRepository)
    {

        $reference = ($request->query->get('reference')) == "" ? "all" : $request->query->get('reference');
        $event = ($request->query->get('event')) == "" ? "all" : $request->query->get('event');
        $eventdate = ($request->query->get('eventdate')) == "" ? "all" : $request->query->get('eventdate');
        $eventticket = ($request->query->get('eventticket')) == "" ? "all" : $request->query->get('eventticket');
        $user = ($request->query->get('user')) == "" ? "all" : $request->query->get('user');
        $organizer = ($request->query->get('organizer')) == "" ? "all" : $request->query->get('organizer');
        $datefrom = ($request->query->get('datefrom')) == "" ? "all" : $request->query->get('datefrom');
        $dateto = ($request->query->get('dateto')) == "" ? "all" : $request->query->get('dateto');

        $status = 1;
        $authChecker->isGranted("ROLE_ATTENDEE") ? $status = "all" : 1;

        $fullevent = $eventRepository->findBySlug($event);
        $ordersQuery = $services->getOrdersForEvent(array("event" => $fullevent, "reference" => $reference, "eventdate" => $eventdate, "eventticket" => $eventticket, "user" => $user, "organizer" => $organizer, "datefrom" => $datefrom, "dateto" => $dateto, "status" => $status))->getQuery();
        $ordersPagination = $paginator->paginate($ordersQuery, $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        $eventname = $fullevent->getName();
        $ismultiticketevent = $fullevent->getOrganizer()->getUser()->isIsbusinessweek() ?: false;

        return $this->render('Dashboard/Shared/Order/attendees.html.twig', [
            'orders' => $ordersPagination,
            'title' => $eventname,
            'event' => $fullevent,
            'ismultiticketevent' => $ismultiticketevent
        ]);
    }

    /**
     *
     */
#[Route("/administrator/manage-attendees", name: "dashboard_administrator_attendees")]
    public function attendeesAdmin(Request $request, AppServices $services, PaginatorInterface $paginator, AuthorizationCheckerInterface $authChecker, EventRepository $eventRepository)
    {

        $reference = ($request->query->get('reference')) == "" ? "all" : $request->query->get('reference');
        $event = ($request->query->get('event')) == "" ? "all" : $request->query->get('event');
        $eventdate = ($request->query->get('eventdate')) == "" ? "all" : $request->query->get('eventdate');
        $eventticket = ($request->query->get('eventticket')) == "" ? "all" : $request->query->get('eventticket');
        $user = ($request->query->get('user')) == "" ? "all" : $request->query->get('user');
        $organizer = ($request->query->get('organizer')) == "" ? "all" : $request->query->get('organizer');
        $datefrom = ($request->query->get('datefrom')) == "" ? "all" : $request->query->get('datefrom');
        $dateto = ($request->query->get('dateto')) == "" ? "all" : $request->query->get('dateto');
        $status = 1;
        $paymentgateway = ($request->query->get('paymentgateway')) == "" ? "all" : $request->query->get('paymentgateway');

        $authChecker->isGranted("ROLE_ATTENDEE") ? $status = "all" : $upcomingtickets = "all";

        $ordersQuery = $services->getOrders(array("reference" => $reference, "event" => $event, "eventdate" => $eventdate, "eventticket" => $eventticket, "user" => $user, "organizer" => $organizer, "datefrom" => $datefrom, "dateto" => $dateto, "status" => $status, "paymentgateway" => $paymentgateway))->getQuery();


        $ordersPagination = $paginator->paginate($ordersQuery, $request->query->getInt('page', 1), 10, array('wrap-queries' => true));

        $event = $eventRepository->findBySlug($event);

        return $this->render('Dashboard/Shared/Order/attendees.html.twig', [
            'orders' => $ordersPagination,
            'event' => $event
        ]);
    }

    /**
     *
     */
#[Route("/administrator/manage-attendees-details/{eventslug}", name: "dashboard_administrator_attendees_details")]
    public function attendeesAdminDetails(AppServices $appServices, $eventslug, OrderRepository $orderRepository, UserRepository $userRepository, EventRepository $eventRepository)
    {
        $users = $appServices->findUsersAttendeedByEventSlug($eventslug);
        $event = $eventRepository->findBySlug($eventslug);

        foreach ($users as $user) {
            $attendedDates = $appServices->findEventDatesAttendedByUser($user, $event);
            $unattendedDates = $appServices->getEventDatesByEvent($event);

            foreach ($attendedDates as $attendedDate) {
                $attendedDate->setAttendeedthiseventdate(true);
            }

            $allEventDates = array_merge($unattendedDates, $event->getEventdates()->toArray());

            $seenIds = [];
            $filteredEventDates = array_filter($allEventDates, static function ($eventDate) use (&$seenIds) {
                if (isset($seenIds[$eventDate->getId()])) {
                    return false; // Duplicate, filter it out
                }
                $seenIds[$eventDate->getId()] = true;
                return true; // Not a duplicate, keep it
            });

            $user->setAttendedevents($attendedDates);
            $user->setAttendedeventscount(count($attendedDates));
        }

        return $this->render('Dashboard/Shared/Order/admin-attendees-details.html.twig', [
            'users' => $users,
            'event' => $event
        ]);
    }


    /**
     *
     */
#[Route("/organizer/manage-attendees-details/{eventslug}", name: "dashboard_organizer_attendees_details")]
    public function attendeesAdminDetailsasOrganizer(AppServices $appServices, $eventslug, OrderRepository $orderRepository, UserRepository $userRepository, EventRepository $eventRepository)
    {
        $users = $appServices->findUsersAttendeedByEventSlug($eventslug);

        $event = $eventRepository->findBySlug($eventslug);

        foreach ($users as $user) {
            $attendedDates = $appServices->findEventDatesAttendedByUser($user, $event);
            $unattendedDates = $appServices->getEventDatesByEvent($event);

            foreach ($attendedDates as $attendedDate) {
                $attendedDate->setAttendeedthiseventdate(true);
            }

            $allEventDates = array_merge($unattendedDates, $event->getEventdates()->toArray());

            $seenIds = [];
            $filteredEventDates = array_filter($allEventDates, static function ($eventDate) use (&$seenIds) {
                if (isset($seenIds[$eventDate->getId()])) {
                    return false; // Duplicate, filter it out
                }
                $seenIds[$eventDate->getId()] = true;
                return true; // Not a duplicate, keep it
            });

            $user->setAttendedevents($attendedDates);
            $user->setAttendedeventscount(count($attendedDates));
        }

        return $this->render('Dashboard/Shared/Order/attendees-details.html.twig', [
            'users' => $users,
            'event' => $event
        ]);
    }


    /**
     *
     */
#[Route("/organizer/manage-attendees-details-event-dates/{userid}/{eventid}", name: "attendace_details_see_attendeed_event_dates")]
    public function attendeesAdminDetailsasOrganizerEventdates($userid, $eventid, AppServices $appServices, OrderRepository $orderRepository, UserRepository $userRepository, EventRepository $eventRepository)
    {
        $event = $eventRepository->find($eventid);
        $user = $userRepository->find($userid);

        $attendedDates = $appServices->findEventDatesAttendedByUser($user, $event);
        $unattendedDates = $appServices->getEventDatesByEvent($event);

        foreach ($attendedDates as $attendedDate) {
            $attendedDate->setAttendeedthiseventdate(true);
        }

        $allEventDates = array_merge($unattendedDates, $event->getEventdates()->toArray());

        $seenIds = [];
        $filteredEventDates = array_filter($allEventDates, static function ($eventDate) use (&$seenIds) {
            if (isset($seenIds[$eventDate->getId()])) {
                return false; // Duplicate, filter it out
            }
            $seenIds[$eventDate->getId()] = true;
            return true; // Not a duplicate, keep it
        });

        return $this->render('Dashboard/Shared/Order/eventdates-details.html.twig', [
            'user' => $user,
            'event' => $event,
            'eventdates' => $filteredEventDates
        ]);
    }

    /**
     *
     */
#[Route("/administrator/manage-attendees-details-event-dates/{userid}/{eventid}", name: "admin_attendace_details_see_attendeed_event_dates")]
    public function attendeesAdminDetailsasEventdates($userid, $eventid, AppServices $appServices, OrderRepository $orderRepository, UserRepository $userRepository, EventRepository $eventRepository)
    {
        $event = $eventRepository->find($eventid);
        $user = $userRepository->find($userid);

        $attendedDates = $appServices->findEventDatesAttendedByUser($user, $event);
        $unattendedDates = $appServices->getEventDatesByEvent($event);

        foreach ($attendedDates as $attendedDate) {
            $attendedDate->setAttendeedthiseventdate(true);
        }

        $allEventDates = array_merge($unattendedDates, $event->getEventdates()->toArray());

        $seenIds = [];
        $filteredEventDates = array_filter($allEventDates, static function ($eventDate) use (&$seenIds) {
            if (isset($seenIds[$eventDate->getId()])) {
                return false; // Duplicate, filter it out
            }
            $seenIds[$eventDate->getId()] = true;
            return true; // Not a duplicate, keep it
        });
        return $this->render('Dashboard/Shared/Order/eventdates-details.html.twig', [
            'user' => $user,
            'event' => $event,
            'eventdates' => $filteredEventDates
        ]);
    }


    /**
     *
     *
     *
     */
#[Route("/attendee/my-tickets/{reference}", name: "dashboard_attendee_order_details")]
#[Route("/organizer/recent-orders/{reference}", name: "dashboard_organizer_order_details")]
#[Route("/administrator/manage-orders/{reference}", name: "dashboard_administrator_order_details")]
    public function details($reference, AppServices $services)
    {

        $order = $services->getOrders(array("reference" => $reference, "status" => "all"))->getQuery()->getOneOrNullResult();
        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $services->redirectToReferer('orders');
        }

        $status = null;

        return $this->render('Dashboard/Shared/Order/details.html.twig', [
            'order' => $order,
            'status' => $status
        ]);
    }

    /**
     *
     */
#[Route("/attendee/manage-orders/{reference}/cancel", name: "dashboard_attendee_order_cancel", methods: ["POST"])]
    public function cancel($reference, Request $request, AppServices $services)
    {
        $order = $services->getOrders(array("reference" => $reference, "status" => "all"))->getQuery()->getOneOrNullResult();

        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $services->redirectToReferer('orders');
        }

        if ($order->getDeletedAt()) {
            $this->addFlash('error', 'The order has been soft deleted, restore it before canceling it');
            return $services->redirectToReferer('orders');
        }

        if ($order->getStatus() != 0 && $order->getStatus() != 1) {
            $this->addFlash('error', 'The order status must be paid or Awaiting');
            return $services->redirectToReferer('orders');
        }

        $services->cancelticket($order->getReference(), $request->query->get('note'));

        $this->addFlash('notice', 'The ticket has been canceled');

        return $services->redirectToReferer('orders');
    }

    /**
     *
     */
#[Route("/administrator/manage-orders/{reference}/cancel", name: "dashboard_administrator_order_cancel", methods: ["POST"])]
    public function cancel_administrator($reference, Request $request, AppServices $services)
    {

        $order = $services->getOrders(array("reference" => $reference, "status" => "all"))->getQuery()->getOneOrNullResult();

        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $services->redirectToReferer('orders');
        }

        if ($order->getDeletedAt()) {
            $this->addFlash('error', 'The order has been soft deleted, restore it before canceling it');
            return $services->redirectToReferer('orders');
        }

        if ($order->getStatus() != 0 && $order->getStatus() != 1) {
            $this->addFlash('error', 'The order status must be paid or Awaiting');
            return $services->redirectToReferer('orders');
        }

        $services->cancelticket($order->getReference(), $request->query->get('note'));

        $this->addFlash('error', 'The ticket has been canceled');

        return $services->redirectToReferer('orders');
    }

    /**
     *
     */
#[Route("/attendee/manage-orders/{reference}/delete", name: "dashboard_attendee_order_delete", methods: ["POST"])]
    public function delete($reference, Request $request, AppServices $services, OrderElementRepository $orderElementRepository, OrderRepository $orderRepository, EventTicketRepository $eventTicketRepository)
    {
        $order = $services->getOrders(array("reference" => $reference, "status" => "all"))->getQuery()->getOneOrNullResult();
        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $services->redirectToReferer('orders');
        }

        $em = $this->getDoctrine()->getManager();

        $eventticketid = $orderElementRepository->findEventTicketsByOrder($order)[0]->getEventticket();
        $eventticket = $eventTicketRepository->findById($eventticketid);

        $attendeedpeopleinthisevnet = $orderRepository->countOrdersWithStatusAboveOneForEventTicket($eventticket);

        if (($attendeedpeopleinthisevnet - 1) < 0.75 * $eventticket->getQuantity()) {
            $eventticket->setInformedcreator(false);
        }

        $em->persist($eventticket);
        $em->flush();
        $em->remove($order);
        $em->flush();

        if ($order->getDeletedAt()) {
            $this->addFlash('error', 'The ticket is deleted');
        } else {
            $this->addFlash('notice', 'The ticket has been deleted');
        }
        if ($request->query->get('forceRedirect') == "1") {
            return $this->redirectToRoute("dashboard_administrator_orders");
        }

        return $services->redirectToReferer('orders');
    }

    /**
     *
     */
#[Route("/administrator/manage-orders/{reference}/delete", name: "dashboard_administrator_order_delete", methods: ["POST"])]
    public function delete_administrator($reference, Request $request, AppServices $services)
    {

        $order = $services->getOrders(array("reference" => $reference, "status" => "all"))->getQuery()->getOneOrNullResult();
        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $services->redirectToReferer('orders');
        }
        $em = $this->getDoctrine()->getManager();

        if ($order->getDeletedAt()) {
            $this->addFlash('error', 'The ticket is deleted');
        } else {
            $this->addFlash('notice', 'The ticket has been deleted');
        }

        if ($order->getPayment()) {
            $order->getPayment()->setOrder(null);
            $em->persist($order);
            $em->persist($order->getPayment());
            $em->flush();
        }

        $em->remove($order);
        $em->flush();

        if ($request->query->get('forceRedirect') == "1") {
            return $this->redirectToRoute("dashboard_administrator_orders");
        }

        return $services->redirectToReferer('orders');
    }


    /**
     *
     */
#[Route("/administrator/manage-orders/{reference}/restore", name: "dashboard_administrator_order_restore", methods: ["POST"])]
    public function restore($reference, AppServices $services)
    {

        $order = $services->getOrders(array("reference" => $reference, "status" => "all"))->getQuery()->getOneOrNullResult();
        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $services->redirectToReferer('orders');
        }

        $order->setDeletedAt(null);
        foreach ($order->getOrderelements() as $orderelement) {
            $orderelement->setDeletedAt(null);
            foreach ($orderelement->getTickets() as $ticket) {
                $ticket->setDeletedAt(null);
            }
            foreach ($orderelement->getTicketsReservations() as $ticketReservation) {
                $ticketReservation->setDeletedAt(null);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
        $this->addFlash('success', 'The order has been succesfully restored');

        return $services->redirectToReferer('orders');
    }

    /**
     *
     */
#[Route("/print-tickets/{reference}", name: "dashboard_tickets_print")]
    public function printTickets($reference, Request $request, AppServices $services, EventDateRepository $eventDateRepository)
    {
        $order = $services->getOrders(array("reference" => $reference))->getQuery()->getOneOrNullResult();
        $eventlink = $request->query->get('link');

        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $this->redirectToRoute("dashboard_attendee_orders");
        }

        $eventDateTicketReference = $request->query->get('event', 'all');
        $startdate = $eventDateRepository->findById($order->getOrderelements()[0]->getEventticket()->getEventdate()->getId())->getStartDate();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);


        $qrCodeData = $order->getId() . "/" . $order->getReference();
        $qrCode = new QrCode($qrCodeData);
        $qrCode->setEncoding(new Encoding('UTF-8'));
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::Low);

        $renderer = new PngWriter();
        $qrCodeImage = $renderer->write($qrCode);
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeImage->getString());

        $eventname = $order->getOrderelements()[0]->getEventticket()->getEventdate()->getEvent()->getName();
        $ticketname = $order->getOrderelements()[0]->getEventticket()->getName();

        $overbook = $order->getOverbook();

        $html = $this->renderView('Dashboard/Shared/Order/ticket-pdf.html.twig', [
            'order' => $order,
            'eventname' => $eventname,
            'ticketname' => $ticketname,
            'startdate' => $startdate,
            'eventDateTicketReference' => $eventDateTicketReference,
            'qrCodeImage' => $qrCodeBase64,
            'overbooked' => $overbook,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($order->getReference() . "-" . "tickets", [
            "Attachment" => false
        ]);
        exit(0);
    }

    /**
     *
     *
     */
#[Route("/organizer/recent-orders/{reference}/resend-confirmation-email", name: "dashboard_organizer_order_resend_confirmation_email", methods: ["POST"])]
#[Route("/administrator/manage-orders/{reference}/resend-confirmation-email", name: "dashboard_administrator_order_resend_confirmation_email", methods: ["POST"])]
    public function resendConfirmationEmail($reference, Request $request, AppServices $services)
    {
        $order = $services->getOrders(array("reference" => $reference, "status" => "all"))->getQuery()->getOneOrNullResult();
        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $services->redirectToReferer('orders');
        }
        $services->sendOrderConfirmationEmail($order, $request->query->get('email'));
        $this->addFlash('success', 'The confirmation email has been resent to' . ' ' . $request->query->get('email'));
        return $services->redirectToReferer('orders');
    }

    /**
     *
     *
     */
#[Route("/administrator/manage-orders/{reference}/validate", name: "dashboard_administrator_order_validate", methods: ["POST"])]
#[Route("/organizer/recent-orders/{reference}/validate", name: "dashboard_organizer_order_validate", methods: ["POST"])]
    public function validate($reference, AppServices $services)
    {

        $order = $services->getOrders(array("reference" => $reference, "status" => 0))->getQuery()->getOneOrNullResult();

        if (!$order) {
            $this->addFlash('error', 'The order can not be found');
            return $services->redirectToReferer('orders');
        }

        if ($order->getDeletedAt()) {
            $this->addFlash('error', 'The order has been soft deleted, restore it before canceling it');
            return $services->redirectToReferer('orders');
        }

        $services->orderticket($order->getReference());

        $this->addFlash('success', 'The order has been successfully validated');

        return $services->redirectToReferer('orders');
    }
}
