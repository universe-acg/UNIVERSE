<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Entity\CartElement;
use App\Entity\Event;
use App\Entity\EventTicket;
use App\Entity\Order;
use App\Entity\OrderElement;
use App\Entity\PaymentGateway;
use App\Repository\EventTicketRepository;
use App\Service\AppServices;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CartController extends Controller
{


//            $em = $this->getDoctrine()->getManager();
//
//        // Remove previous ticket reservations
//        if (count($this->getUser()->getTicketreservations())) {
//            foreach ($this->getUser()->getTicketreservations() as $ticketreservation) {
//                $em->remove($ticketreservation);
//            }
//            $em->flush();
//        }
//
//        // Check event sale status
//        foreach ($this->getUser()->getCartelements() as $cartelement) {
//            if (!$cartelement->getEventticket()->isOnSale()) {
//                $em->remove($cartelement);
//                $em->flush();
////                $this->addFlash('notice', $translator->trans('Your cart has been automatically updated because one or more events are no longer on sale'));
////                return $this->redirectToRoute("dashboard_attendee_cart");
//            }
//            if (!$cartelement->getEventticket()->isOnSale()) {
//                $em->remove($cartelement);
//                $em->flush();
////                $this->addFlash('notice', $translator->trans('Your cart has been automatically updated because one or more events are no longer on sale'));
////                return $this->redirectToRoute("dashboard_attendee_cart");
//            }
//            if ($cartelement->getEventticket()->getTicketsLeftCount() > 0 && $cartelement->getQuantity() > $cartelement->getEventticket()->getTicketsLeftCount()) {
//                $cartelement->setQuantity($cartelement->getEventticket()->getTicketsLeftCount());
//                $em->persist($cartelement);
//                $em->flush();
////                $this->addFlash('notice', $translator->trans('Your cart has been automatically updated because one or more event\'s quotas has changed'));
////                return $this->redirectToRoute("dashboard_attendee_cart");
//            }
//        }
//        if ($request->getMethod() == "POST") {
//            if (count($request->request->all()) == 0) {
//                $this->addFlash('notice', $translator->trans('No tickets selected to add to cart'));
//            } else {
//                foreach ($request->request->all() as $ticketreference => $ticketquantity) {
//                    $cartelement = $this->getUser()->getCartelementByEventTicketReference($ticketreference);
//                    if ($cartelement) {
//                        $cartelement->setQuantity($ticketquantity);
//                        $em->persist($cartelement);
////                        $this->addFlash('error', $translator->trans('The event ticket can not be found'));
////                        return $this->render('Dashboard/Attendee/Cart/cart.html.twig');
//                    }
//
//                }
//                $em->flush();
////                $this->addFlash('success', $translator->trans('Your cart has been updated'));
//            }
//        }
//
//        // previous code ends
//
//
//        if ($this->isGranted("ROLE_ATTENDEE")) {
//            $paymentGateways = $services->getPaymentGateways(array())->getQuery()->getResult();
//            $form = $this->createForm(CheckoutType::class, null, array('validation_groups' => 'attendee'));
//        } else {
//            $form = $this->createForm(CheckoutType::class, null, array('validation_groups' => 'pos'));
//        }
//
//        $this->addFlash('error', $translator->trans('adsadadsaddaa'));
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted()) {
//
//            $order = $services->getOrders(array('status' => 0, 'reference' => $form->getData()['orderReference']))->getQuery()->getOneOrNullResult();
//
//            if ($form->isValid()) {
//                $storage = $this->get('payum')->getStorage(Payment::class);
//
//                $orderTotalAmount = $order->getOrderElementsPriceSum(true);
//
//                if ($orderTotalAmount == 0) {
//                    $paymentGateway = $em->getRepository(PaymentGateway::class)->findOneBySlug("free");
//                    $gatewayFactoryName = "offline";
//                }
//
//// Sets the choosen payment gateway
//                $order->setPaymentGateway($paymentGateway);
//                $em->persist($order);
//
//// Sets the amount to be paid
//                $orderamount = (int)bcmul($orderTotalAmount, 100);
//
//                $payment = $storage->create();
//                $payment->setOrder($order);
//                $payment->setNumber($services->generateReference(20));
//                $payment->setCurrencyCode($services->getSetting("currency_ccy"));
//                $payment->setTotalAmount($orderamount); // 1.23 USD = 123
//                $payment->setDescription($translator->trans("Payment of tickets purchased on %website_name%", array('%website_name%' => $services->getSetting("website_name"))));
//                $payment->setClientId($this->getUser()->getId());
//                if ($form->getData()['firstname'] != null && $form->getData()['firstname'] != "") {
//                    $payment->setFirstname($form->getData()['firstname']);
//                }
//                if ($form->getData()['lastname'] != null && $form->getData()['lastname'] != "") {
//                    $payment->setLastname($form->getData()['lastname']);
//                }
//                if ($this->isGranted("ROLE_ATTENDEE")) {
//                    $payment->setClientEmail($form->getData()['email']);
//                    $payment->setCountry($form->getData()['country']);
//                    $payment->setState($form->getData()['state']);
//                    $payment->setCity($form->getData()['city']);
//                    $payment->setPostalcode($form->getData()['postalcode']);
//                    $payment->setStreet($form->getData()['street']);
//                    $payment->setStreet2($form->getData()['street2']);
//                }
//
//                $storage->update($payment);
//                $order->setPayment($payment);
//                $em->flush();
//
//                if ($this->isGranted("ROLE_ATTENDEE")) {
//                    if ($request->request->get('payment_gateway') == "offline") {
//                        $this->addFlash('success', $translator->trans('Successful'));
//                        return $this->redirectToRoute("dashboard_attendee_order_details", ['reference' => $payment->getOrder()->getReference()]);
//                    }
//
//                    $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
//                        $gatewayFactoryName, $payment, 'dashboard_attendee_checkout_done'
//                    );
//                } else {
//                    $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
//                        $gatewayFactoryName, $payment, 'dashboard_pointofsale_checkout_done'
//                    );
//                }
//
//                return $this->redirect($captureToken->getTargetUrl());
//            }
//
//            $this->addFlash('error', $translator->trans('The form contains invalid data'));
//
//            return $this->render('Dashboard/Attendee/Order/checkout.html.twig', [
//                'form' => $form->createView(),
//                'paymentGateways' => $paymentGateways,
//                'order' => $order
//            ]);
//        }
//
//        return $this->redirectToRoute("dashboard_index");

    /**
     * @Route("/attendee/bookevent/{reference}", name="dashboard_attendee_bookevent")
     */
    public function bookevent(Request $request, TranslatorInterface $translator, AppServices $services, EventTicketRepository $eventTicketRepository, RouterInterface $router)
    {
        $em = $this->getDoctrine()->getManager();
        $reference = $request->request->get('reference');
        $event = $em->getRepository(Event::class)->findOneBy(['id' => $reference]);

        $eventticket = $eventTicketRepository->findByEventTicketId($event->getId());

        if (!$event) {
            $this->addFlash('error', $translator->trans('The event can not be found'));
            return $this->redirectToRoute("dashboard_index"); // Redirect to the dashboard or appropriate route
        }

        // Assuming you have access to the user, payment gateway, and order elements
        $user = $this->getUser();
        $paymentGateway = $this->getDoctrine()->getRepository(PaymentGateway::class)->findOneBySlug("free");

        // Create a new Order entity
        $order = new Order();
        $order->setUser($user);
        $order->setPaymentGateway($paymentGateway);
        $order->setTicketFee(0);
        $order->setTicketPricePercentageCut(0);
        $order->setTicketPricePercentageCut(0);

        $orderelement = new OrderElement();
        $orderelement->setOrder($order);
        $orderelement->setEventticket($eventticket[0]);
        $orderelement->setUnitprice(0);
        $orderelement->setQuantity(1);

        $order->addOrderelement($orderelement);

        $order->setReference($this->generateReference(15)); // Generate a unique order reference.
        $order->setStatus(1); // Set the initial status (e.g., waiting for payment).

        // Persist the order to the database
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($order);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('Event has been booked'));

        return $this->redirectToRoute("dashboard_index");
    }

    /**
     * @throws Exception|Exception
     */
    public function generateReference($length): string
    {
        $reference = implode('', [
            bin2hex(random_bytes(2)),
            bin2hex(random_bytes(2)),
            bin2hex(chr((ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
            bin2hex(chr((ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
            bin2hex(random_bytes(2))
        ]);

        return strlen($reference) > $length ? substr($reference, 0, $length) : $reference;
    }

    /**
     * @Route("/attendee/cart/{id}/remove", name="dashboard_attendee_cart_remove")
     */
    public function remove($id, TranslatorInterface $translator, AppServices $services)
    {
        $em = $this->getDoctrine()->getManager();
        $cartelement = $em->getRepository(CartElement::class)->find($id);
        if ($cartelement->getUser() != $this->getUser()) {
            $this->addFlash('error', $translator->trans('Access is denied. You may not have the appropriate permissions to access this resource.'));
            return $this->redirectToRoute("dashboard_index");
        }
        $em->remove($cartelement);
        $em->flush();
        $this->addFlash('info', $translator->trans('Your cart has been updated'));
        return $services->redirectToReferer("cart");
    }

    /**
     * @Route("/attendee/cart/empty", name="dashboard_attendee_cart_empty")
     */
    public function emptyCart(TranslatorInterface $translator, AppServices $services)
    {
        $em = $this->getDoctrine()->getManager();
        $services->emptyCart($this->getUser());
        $this->addFlash('info', $translator->trans('Your cart has been emptied'));
        return $services->redirectToReferer("cart");
    }

    /**
     * @Route("attendee/cart/add", name="dashboard_attendee_cart_add")
     * @Route("pointofsale/cart/add", name="dashboard_pointofsale_cart_add")
     */
    public function add(Request $request, TranslatorInterface $translator, AppServices $services)
    {

        $em = $this->getDoctrine()->getManager();

        foreach ($request->request->all() as $ticketreference => $ticketquantity) {
            if ($ticketquantity != "" && (int)$ticketquantity > 0) {
                $eventticket = $em->getRepository(EventTicket::class)->findOneByReference($ticketreference);
                if (!$eventticket) {
                    $this->addFlash('error', $translator->trans('The event ticket can not be found'));
                    return $services->redirectToReferer();
                }
                if (!$eventticket->isOnSale()) {
                    $this->addFlash('error', $eventticket->stringifyStatus());
                    return $services->redirectToReferer();
                }
                $cartelement = new CartElement();
                $cartelement->setUser($this->getUser());
                $cartelement->setEventticket($eventticket);
                $cartelement->setQuantity(intval($ticketquantity));

                if ($this->getUser()->hasRole("ROLE_ATTENDEE") && !$cartelement->getEventticket()->getFree()) {
                    $cartelement->setTicketFee($services->getSetting("ticket_fee_online"));
                } else if ($this->getUser()->hasRole("ROLE_POINTOFSALE") && !$cartelement->getEventticket()->getFree()) {
                    $cartelement->setTicketFee($services->getSetting("ticket_fee_pos"));
                }

                $em->persist($cartelement);
                $em->flush();
            }
        }
        if ($this->isGranted("ROLE_ATTENDEE")) {
            $this->addFlash('success', $translator->trans('The tickets has been successfully reserved'));
            return $this->redirectToRoute("dashboard_attendee_cart");
        }

        return $this->redirectToRoute("dashboard_pointofsale_checkout");
    }

}
