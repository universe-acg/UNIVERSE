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
    use App\Entity\TicketReservation;
    use App\Repository\EventTicketRepository;
    use App\Repository\OrderElementRepository;
    use App\Repository\OrderRepository;
    use App\Service\AppServices;
    use Endroid\QrCode\Encoding\Encoding;
    use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\Writer\PngWriter;
    use Exception;
    use RuntimeException;
    use Swift_Attachment;
    use Swift_Mailer;
    use Swift_Message;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Twig_Environment;
    
    class CartController extends Controller
    {
        
        /**
         * @Route("/attendee/bookevent/{id}/{eventicketid}", name="dashboard_attendee_bookevent")
         */
        public function bookevent(Request $request, AppServices $services, Swift_Mailer $mailer, Twig_Environment $templating, EventTicketRepository $eventTicketRepository, OrderElementRepository $orderElementRepository, OrderRepository $orderRepository)
        {
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');
            $eventicketid = $request->get('eventicketid');
            
            
            $user = $this->getUser();
            
            $orders = $orderRepository->getOrdersByUser($user);
            $eventDates = [];

// Iterate through the user's orders and order elements
            foreach ($orders as $order) {
                $orderElements = $order->getOrderelements();
                
                foreach ($orderElements as $orderElement) {
                    // Get the event date associated with the order element
                    $eventDate = $orderElement->getEventticket()->getEventdate();
                    
                    // Add the event date to the array if it's not already included
                    if (!in_array($eventDate, $eventDates, true)) {
                        $eventDates[] = $eventDate;
                    }
                }
            }
            
            foreach ($eventDates as $eventDate) {
                $referer = $request->headers->get('referer');
                if ($eventDate->getTickets()[0]->getActive()) {
                    if ($eventDate->getTickets()[0]->getId() == $eventicketid) {
                        $this->addFlash('error', 'You have already booked this ticket');
                        return $this->redirect($referer);
                    }
                }
            }
            
            
            $event = $em->getRepository(Event::class)->findOneBy(['id' => $id]);
            
            $eventticket = $eventTicketRepository->findById($eventicketid);

//            find how many have booked this ticket
            $countOrdersForTicket = $eventTicketRepository->countOrdersForTicket($eventticket);
            
            if (!$event) {
                $this->addFlash('error', 'The event can not be found');
                return $this->redirectToRoute("dashboard_index"); // Redirect to the dashboard or appropriate route
            }
            
            // Assuming you have access to the user, payment gateway, and order elements
            $user = $this->getUser();
            
            // Create a new Order entity
            $order = new Order();
            $order->setUser($user);
            
            $orderelement = new OrderElement();
            $orderelement->setOrder($order);
            $orderelement->setEventticket($eventticket);
            $orderelement->setQuantity(1);
            
            $order->addOrderelement($orderelement);
            
            $order->setReference($this->generateReference(15)); // Generate a unique order reference.
            $order->setStatus(1); // Set the initial status (e.g., waiting for payment).
            if (($countOrdersForTicket + 1) > $eventticket->getQuantity()) {
                $order->setOverbook(true);
            }
            // Persist the order to the database
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($order);
            $entityManager->flush();
            
            foreach ($order->getOrderelements() as $orderElement) {
                $ticketreservation = new TicketReservation();
                $ticketreservation->setEventticket($orderElement->getEventticket());
                $ticketreservation->setUser($this->getUser());
                $ticketreservation->setOrderelement($orderElement);
                $ticketreservation->setQuantity(1);
                $orderElement->addTicketsReservation($ticketreservation);
                $em->persist($ticketreservation);
                $em->flush();
            }
            
            $this->addFlash('success', 'Event has been booked');
            $qrCodeData = $order->getId() . "/" . $order->getReference();
            $qrCode = new QrCode($qrCodeData);
            $qrCode->setEncoding(new Encoding('UTF-8'));
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelLow());
            
            // Render the QR code as a PNG image
            $renderer = new PngWriter();
            $qrCodeImage = $renderer->write($qrCode);
            
            $emaildata = [
                $titleofeventandticket = $event->getName() . " | " . $eventticket->getName(),
                $startdate = $eventticket->getEventdate()->getStartdate()->format('Y-m-d H:i:s'),
                $ticketcode = $eventticket->getReference(),
                $link = $request->headers->get('referer')
            ];
            
            $message = "You have just booked this event: " . $titleofeventandticket;
            
            // send email to organizer and user
            $email = (new Swift_Message($services->getSetting('website_name') . ' - ' . $event->getName() . ' event booked' . ' #' . $eventticket->getReference()))
                ->setFrom($services->getSetting('no_reply_email'))
                ->setTo($this->getUser()->getEmail())
                ->setBody(
                    $templating->render('Dashboard/Shared/Order/ticket-booked-email.html.twig', ['data' => $emaildata, 'message' => $message]), 'text/html', 'UTF-8');
            
            // Load the PNG image file
            // Save as a temporary PNG file
            $tempDirectory = $this->getParameter('kernel.project_dir') . '/var';
            if (!file_exists($tempDirectory) && !mkdir($tempDirectory, 0777, true) && !is_dir($tempDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $tempDirectory));
            }
            $tempPngFile = $tempDirectory . '/qr_code.png';
            file_put_contents($tempPngFile, $qrCodeImage->getString());
            $pngAttachment = Swift_Attachment::fromPath($tempPngFile, 'image/png');
            $pngAttachment->setFilename('qr_code.png'); // Set the desired filename
            $email->attach($pngAttachment);
            
            try {
                $result = $mailer->send($email);
            } catch (Exception $e) {
                $this->addFlash('danger', "The event booking reminder email could not be sent");
            }
            
            $attendeedpeopleinthisevnet = $orderRepository->countOrdersWithStatusAboveOneForEventTicket($eventticket);
            
            if (!$eventticket->getInformedcreator() && $attendeedpeopleinthisevnet >= 0.75 * $eventticket->getQuantity()) {
                
                $eventticket->setInformedcreator(true);
                // if the tickets booked or more than 0.75 of quantity... inform teh creator
                $emaildata = [
                    $title = $event->getName(),
                    $startdate = $eventticket->getEventdate()->getStartdate()->format('Y-m-d H:i:s'),
                    $link = $request->headers->get('referer')
                ];
                
                $message = "The event you created: " . $title . ", on the event date of: " . $eventticket->getEventdate()->getStartdate()->format('Y-m-d H:i:s') . " is filling up. More than 75% of capacity is booked.";
                
                // send email to organizer and user
                $email = (new Swift_Message($services->getSetting('website_name') . ' - ' . $event->getName() . ' event booked' . ' #' . $eventticket->getReference()))
                    ->setFrom($services->getSetting('no_reply_email'))
                    ->setTo($event->getOrganizer()->getEmail())
                    ->setBody(
                        $templating->render('Dashboard/Shared/Order/ticket-booked-email.html.twig', ['data' => $emaildata, 'message' => $message]), 'text/html', 'UTF-8');
                
                try {
                    $result = $mailer->send($email);
                } catch (Exception $e) {
                }
            }
            
            $em->persist($eventticket);
            $em->flush();
            
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
