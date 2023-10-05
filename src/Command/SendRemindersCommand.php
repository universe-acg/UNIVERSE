<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

// src/Command/SendRemindersCommand.php
    
    namespace App\Command;
    
    // Include necessary Symfony components
    use App\Entity\Event;
    use App\Entity\Order;
    use App\Entity\OrderElement;
    use App\Entity\TicketReservation;
    use App\Service\AppServices;
    use DateTime;
    use Doctrine\ORM\EntityManagerInterface;
    use Endroid\QrCode\Encoding\Encoding;
    use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\Writer\PngWriter;
    use Exception;
    use RuntimeException;
    use Swift_Attachment;
    use Swift_Mailer;
    use Swift_Message;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\Mailer\MailerInterface;
    use Twig_Environment;
    
    class SendRemindersCommand extends Command
    {
        private $templating;
        
        private $mailer;
        private $entityManager;
        
        private $appServices;
        
        public function __construct(Swift_Mailer $mailer, AppServices $appServices, EntityManagerInterface $entityManager, Twig_Environment $templating)
        {
            parent::__construct();
            
            $this->mailer = $mailer;
            $this->entityManager = $entityManager;
            $this->templating = $templating;
            $this->appServices = $appServices;
        }
        
        protected function configure()
        {
            $this
                ->setName('app:send-reminders')
                ->setDescription('Send reminders to attendees of events starting today.');
        }
        
        protected function execute(InputInterface $input, OutputInterface $output)
        {
            // Get events starting today from your database
            $today = new DateTime('today');
            $events = $this->entityManager->getRepository(Event::class)->findEventsStartingOn($today);
            
            // Iterate through events and send reminders
            foreach ($events as $event) {
                // Fetch attendees for the event
                $attendees = $this->appServices->findBookedAttendeesOfEvent($event);
                
                foreach ($attendees as $attendee) {
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
                    
                    
                    $email = (new Swift_Message($this->getSetting('website_name') . ' - Kind reminder, ' . $event->getName() . ' is starting today.'))
                        ->setFrom($this->getSetting('no_reply_email'))
                        ->setTo($attendee->getEmail())
                        ->setBody(
                            $this->templating->render('Dashboard/Shared/Order/event-reminder-email.html.twig', ['data' => $emaildata, 'message' => $message]), 'text/html', 'UTF-8');
                    
                    try {
                        $result = $this->mailer->send($email);
                    } catch (Exception $e) {
                    }
//
//                    // Send a reminder email to each attendee
//                    $email = (new Email())
//                        ->setFrom($this->getSetting('no_reply_email'))
//                        ->to($attendee->getEmail())
//                        ->subject('Reminder: Your event starts today')
//                        ->text('Dear ' . $attendee->getName() . ', your event "' . $event->getName() . '" starts today.');
//
//                    $this->mailer->send($email);
                }
            }
            
            $output->writeln('Reminders sent successfully.');
            
            return Command::SUCCESS;
        }
    }
