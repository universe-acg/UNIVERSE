<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

// src/Command/SendRemindersCommand.php
    
    namespace App\Command;
    
    // Include necessary Symfony components
    use App\Repository\EventDateRepository;
    use App\Repository\EventRepository;
    use App\Repository\UserRepository;
    use App\Service\AppServices;
    use DateTime;
    use Doctrine\ORM\EntityManagerInterface;
    use Endroid\QrCode\Encoding\Encoding;
    use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\Writer\PngWriter;
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
        
        private $eventRepository;
        private $eventDateRepository;
        private $userRepository;
        
        public function __construct(Swift_Mailer $mailer, AppServices $appServices, EntityManagerInterface $entityManager, Twig_Environment $templating, EventRepository $eventRepository, EventDateRepository $eventDateRepository, UserRepository $userRepository)
        {
            parent::__construct();
            
            $this->userRepository = $userRepository;
            $this->eventDateRepository = $eventDateRepository;
            $this->eventRepository = $eventRepository;
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
            // Get the repositories (you can use dependency injection or however it's available)

// Get users with the desired role who have booked tickets
            $role = "ATTENDEE"; // Replace with the actual role
            $usersWithRole = $this->userRepository->getUsersByRole($role);

// Get the current time as a DateTime object
            $currentDateTime = new DateTime('now');

// Define a threshold time for comparison (8 AM today)
            $thresholdTime = new DateTime('today 0:00:00');

// Initialize an array to store the events that meet the criteria
            $eventsMatchingCriteria = [];

// Loop through users
            foreach ($usersWithRole as $user) {
                // Call the function to get tickets booked by the user
                $events = $this->eventRepository->getTicketsBookedByUser($user);
                // Loop through the tickets booked by the user
                foreach ($events as $event) {
                    
                    $eventDates = $this->eventDateRepository->getEventDates("all", "all", $event->getSlug(), 1, false)->getQuery()->getResult();
                    
                    foreach ($eventDates as $eventDate) {
                        $now = new DateTime();

// Calculate the start time (8 AM) and end time (midnight) for today
                        $startTime = new DateTime($now->format('Y-m-d') . ' 08:00:00');
                        $endTime = new DateTime($now->format('Y-m-d') . ' 23:59:59');
                        
                        $startDate = $eventDate->getStartdate();
                        
                        if ($startDate >= $startTime && $startDate <= $endTime) {
                            // This event date falls within the time range (8 AM to midnight)
                            // You can do something with this event date, like adding it to a list of events
                            $message = "Kind reminder, you have booked event(s) for today.";
                            
                            $emaildata = [
                                $title = $event->getName() . " | " . $eventDate->getTickets()[0]->getName(),
                                $startdate = $eventDate->getStartdate()->format('D j M Y, g:i A'),
                                $link = "https://universeacg.xyz/event/" . $event->getSlug(),
                            ];
                            $email = (new Swift_Message($this->appServices->getSetting('website_name') . ' - Kind reminder, ' . $event->getName() . ' is taking place today'))
                                ->setFrom($this->appServices->getSetting('no_reply_email'))
                                ->setTo("e.kardasis@acg.edu")
                                ->setBody(
                                    $this->templating->render('Dashboard/Shared/Order/event-daily-reminder-email.html.twig', ['data' => $emaildata, 'message' => $message, 'services' => $this->appServices]), 'text/html', 'UTF-8');
                            
                            $this->mailer->send($email);
                        }
                    }
                }
            }
            
            
            $output->writeln('Reminders sent successfully.');
            
            return Command::SUCCESS;
        }
    }
