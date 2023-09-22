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

    /**
     * @Route("/attendee/bookevent/{reference}", name="dashboard_attendee_bookevent")
     */
    public function bookevent(Request $request, AppServices $services, EventTicketRepository $eventTicketRepository, RouterInterface $router)
    {
        $em = $this->getDoctrine()->getManager();
        $reference = $request->request->get('reference');
        $event = $em->getRepository(Event::class)->findOneBy(['id' => $reference]);

        $eventticket = $eventTicketRepository->findByEventTicketId($event->getId());

        if (!$event) {
            $this->addFlash('error', 'The event can not be found');
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
    public function remove($id, AppServices $services)
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
    public function add(Request $request, AppServices $services)
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
