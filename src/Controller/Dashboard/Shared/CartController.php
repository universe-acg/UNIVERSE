<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Entity\CartElement;
use App\Entity\Event;
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
            return $this->redirectToRoute("dashboard_index");
        }

        // Assuming you have access to the user, payment gateway, and order elements
        $user = $this->getUser();

        // Create a new Order entity
        $order = new Order();
        $order->setUser($user);
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

        $this->addFlash('success', 'Event has been booked');

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
            $this->addFlash('error', 'Access is denied. You may not have the appropriate permissions to access this resource.');
            return $this->redirectToRoute("dashboard_index");
        }
        $em->remove($cartelement);
        $em->flush();
        $this->addFlash('info', 'Your cart has been updated');
        return $services->redirectToReferer("cart");
    }
}
