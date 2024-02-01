<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Dashboard\Shared;
    
    use App\Entity\Event;
    use App\Entity\EventDate;
    use App\Entity\EventFile;
    use App\Form\EventTypeMulti;
    use App\Form\EventTypeMultiEdit;
    use App\Form\EventTypeMultiEditSimple;
    use App\Form\EventTypeMultiSimple;
    use App\Message\PublishEventMessage;
    use App\Repository\CategoryRepository;
    use App\Repository\EventFileRepository;
    use App\Repository\EventRepository;
    use App\Repository\OrderRepository;
    use App\Repository\OrganizerRepository;
    use App\Service\AppServices;
    use App\Service\NotificationService;
    use DateTime;
    use Doctrine\ORM\NonUniqueResultException;
    use Exception;
    use Knp\Component\Pager\PaginatorInterface;
    use MaxMind\Db\Reader\InvalidDatabaseException;
    use Psr\Cache\InvalidArgumentException;
    use Swift_Mailer;
    use Swift_Message;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Messenger\MessageBusInterface;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
    use Twig_Environment;
    
    class EventController extends Controller
    {
        
        private $eventRepository;
        
        private $messageBus;
        
        private $notificationService;
        
        public function __construct(NotificationService $notificationService, EventRepository $eventRepository, MessageBusInterface $messageBus)
        {
            $this->eventRepository = $eventRepository;
            $this->messageBus = $messageBus;
            $this->notificationService = $notificationService;
        }
        
        /**
         * @Route("/administrator/manage-events", name="dashboard_administrator_event", methods="GET")
         * @param Request $request
         * @param PaginatorInterface $paginator
         * @param AppServices $services
         * @return Response
         * @throws Exception
         */
        public function indexadmin(Request $request, PaginatorInterface $paginator, AppServices $services): Response
        {
            $events = $services->getEvents(array("sort" => "e.createdAt", "order" => "DESC"));
            $events = $paginator->paginate($events, $request->query->getInt('page', 1), 10, array('wrap-queries' => true));
            
            return $this->render('Dashboard/Shared/Event/index.html.twig', [
                'events' => $events,
            ]);
        }
        
        /**
         * @Route("/organizer/my-events", name="dashboard_organizer_event", methods="GET")
         * @param EventRepository $eventRepository
         * @param Request $request
         * @param PaginatorInterface $paginator
         * @return Response
         * @throws Exception
         */
        public function indexorganizer(EventRepository $eventRepository, Request $request, PaginatorInterface $paginator): Response
        {
            $slug = ($request->query->get('slug')) == "" ? "all" : $request->query->get('slug');
            $category = ($request->query->get('category')) == "" ? "all" : $request->query->get('category');
            $elapsed = ($request->query->get('elapsed')) == "" ? "all" : $request->query->get('elapsed');
            $published = ($request->query->get('published')) == "" ? "all" : $request->query->get('published');
            
            $slug = "all";
            $category = "all";
            $elapsed = true;
            $published = "all";
            
            $organizer = "all";
            
            // Get the events and sort them by "updated_at" in descending order
            $sortedEvents = $eventRepository->getEventsCreatedByUserAndSortByUpdatedAt($this->getUser()->getOrganizer()->getId());
            
            // Paginate the sorted events
            $events = $paginator->paginate(
                $sortedEvents,  // The array to paginate
                $request->query->getInt('page', 1),  // Current page number, default to 1
                10  // Number of items per page
            );
            return $this->render('Dashboard/Shared/Event/index.html.twig', [
                'events' => $events,
            ]);
        }
        
        /**
         * @Route("/organizer/my-events/add", name="dashboard_organizer_event_add", methods="GET|POST")
         * @throws InvalidDatabaseException
         * @throws Exception|InvalidArgumentException
         */
        public function addEvent(
            EventRepository               $eventRepository,
            OrganizerRepository           $organizerRepository,
            Request                       $request,
            AppServices                   $services,
                                          $slug = null,
            AuthorizationCheckerInterface $authChecker,
            Swift_Mailer                  $mailer,
            Twig_Environment              $templating,
            CategoryRepository            $categoryRepository
        )
        {
            $organizer = $this->getUser()->getOrganizer();
            $organizerCategory = $organizer->getCategory();
            $categoryName = $this->mapCategoryName($organizerCategory);
            $category = $this->getCategoryByName($categoryName, $categoryRepository);
            
            $eventChoices = $this->getEventChoices($eventRepository, $organizer, $authChecker);
            if (!$organizer->getCategory()) {
                $eventChoices = $eventRepository->getAllEvents()->getQuery()->getResult();
            }
            
            $event = $this->initializeEvent($slug);
            
            if ($event === null) {
                return $services->redirectToReferer('event');
            }
            
            $formTypesimple = EventTypeMultiSimple::class;
            $formType = EventTypeMulti::class;
            
            $form = $this->createForm($formType, $event, [
                'validation_groups' => ['create', 'Default'],
                'user_choices' => $eventChoices,
            ]);
            
            $formsimple = $this->createForm($formTypesimple, $event, [
                'validation_groups' => ['create', 'Default'],
                'user_choices' => $eventChoices,
            ]);
            
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);
            $formsimple->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                // Check if it's an out-of-campus event
                if ($event->getOutofcampus()) {
                    // If it's an out-of-campus event, set the venue to null
                    $event->setVenue(null);
                } else if (null === $event->getVenue()) {
                    // Set the "outofcampus" field to false
                    $event->setOutofcampus(false);
                }
                if ($event->getRepeatsweekly()) {
                    $commonValue = $this->generateCommonValue();
                    $event->setCommonvalue($commonValue);
                    $event->setIsrepeat(true);
                    $this->processSubmittedEvent($event, $category, $commonValue, $em, $services, $authChecker);
                    $this->processRepeatingEvent($event, $category, $commonValue, $em, $services);
                } else {
                    $this->processFormSubmission($event, $em, $services, $category, $slug);
                    $this->processSubmittedEvent($event, $category, $commonValue, $em, $services, $authChecker);
                }
                
                // Handle new event creation or update
                if (!$slug) {
                    $this->addFlash('success', 'The event has been successfully created');
                } else {
                    $this->addFlash('success', 'The event has been successfully updated');
                }
                
                return $this->redirectToRouteAfterSubmission($authChecker);
            }
            
            if ($formsimple->isSubmitted() && $formsimple->isValid()) {
                // Set references for event dates and tickets
                foreach ($event->getEventdates() as $eventdate) {
                    $eventdate->setEvent($event);
                    if (!$slug || !$eventdate->getReference()) {
                        $eventdate->setReference($services->generateReference(10));
                    }
                    foreach ($eventdate->getTickets() as $eventticket) {
                        $eventdate->setTitle($event->getName());
                        $eventdate->setDescription($event->getDescription());
                        $eventticket->setEventdate($eventdate);
                        if (!$slug || !$eventticket->getReference()) {
                            $eventticket->setReference($services->generateReference(10));
                        }
                    }
                }
                $this->processSubmittedEvent($event, $category, '', $em, $services, $authChecker);
                
                // Handle new event creation or update
                if (!$slug) {
                    $this->addFlash('success', 'The event has been successfully created');
                } else {
                    $this->addFlash('success', 'The event has been successfully updated');
                }
                
                return $this->redirectToRouteAfterSubmission($authChecker);
            }
            
            return $this->renderEventForm($event, $form, $formsimple);
        }
        
        private function mapCategoryName($category)
        {
            // Implement your mapping logic here
            return ($category === 'Club') ? 'Clubs' : (($category === 'Society') ? 'Societies' : ($category === 'Organization' ? 'Organizations' : 'Offices'));
        }
        
        private function getCategoryByName($categoryName, $categoryRepository)
        {
            return $categoryRepository->findByName($categoryName);
        }
        
        private function getEventChoices($eventRepository, $organizer, $authChecker)
        {
            $eventChoices = [];
            
            // Implement logic to fetch event choices, e.g., based on user roles
            if ($authChecker->isGranted('ROLE_ORGANIZER')) {
                // Fetch events for the current organizer
                $events = $eventRepository->getEventsByOrganizerId($organizer->getId())->getQuery()->getResult();
                
            } else {
                // Fetch all events
                $events = $eventRepository->findAll();
            }
            
            // Create an array of event choices
            foreach ($events as $event) {
                $eventKey = $event->getName() . '_' . $event->getId();
                $eventChoices[$eventKey] = $event->getId();
            }
            
            return $eventChoices;
        }
        
        private function initializeEvent($slug)
        {
            if (!$slug) {
                // If no slug is provided, create a new event
                $event = new Event();
            } else {
                $event = $this->getEventBySlug($slug);
                
                if (!$event) {
                    $this->addFlash('error', 'The event can not be found');
                    return null;
                }
            }
            
            return $event;
        }
        
        public function getEventBySlug($slug)
        {
            // Use the repository to find the event by its slug
            $event = $this->eventRepository->findOneBySlug(['slug' => $slug]);
            
            if (!$event) {
                return null;
            }
            
            return $event;
        }
        
        // Helper method to map category name
        
        private function generateCommonValue()
        {
            // Define characters that can be used in the common value
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            
            // Length of the common value
            $length = 40; // You can adjust the length as needed
            
            $commonValue = '';
            
            // Generate a random common value of the specified length
            for ($i = 0; $i < $length; $i++) {
                $randomIndex = random_int(0, strlen($characters) - 1);
                $commonValue .= $characters[$randomIndex];
            }
            
            return $commonValue;
        }
        
        // Helper method to generate a unique slug
        
        private function processSubmittedEvent($event, $category, $commonValue, $em, $services, $authChecker)
        {
            $event->setOrganizer($this->getUser()->getOrganizer());
            $newSlug = $this->generateUniqueSlug($event->getName());
            $event->setSlug($newSlug);
            $event->setCategory($category);
            
            $em->persist($event);
            $em->flush();
            
            if ($event->isPublishOnSpecificDate()) {
                $event->setPublished(false);
                // Get the selected publication date from the form
                $publishDate = $event->getPublishDate();
                
                // Check if the publication date is in the future
                $now = new DateTime(); // Get the current date and time
                if ($publishDate > $now) {
                    // Schedule the publication using Symfony's Messenger component
                    $message = new PublishEventMessage($event->getId());
                    $message->setScheduledAt($publishDate);
                    
                    // Dispatch the message to the Messenger component
                    $this->messageBus->dispatch($message);
                } else {
                    // If the publication date is in the past or now, set the event as published
                    $event->setPublished(true);
                }
            } else {
                $event->setPublished(true);
            }
            $em->persist($event);
            $em->flush();
            
            $this->processFormSubmission($event, $em, $services, $category, $newSlug);
        }
        
        private function generateUniqueSlug($title)
        {
            // Generate a slug based on the title
            $baseSlug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
            
            // Generate a random string (e.g., 6 characters) to append to the slug
            $randomString = bin2hex(random_bytes(10)); // Adjust the number of bytes for desired length
            
            // Combine the base slug and random string
            $uniqueSlug = $baseSlug . '-' . $randomString;
            
            // Check if the generated slug already exists in the database
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Event::class);
            
            // Keep incrementing the random string until you find a unique slug
            $counter = 1;
            while ($repository->findOneBy(['slug' => $uniqueSlug])) {
                $counter++;
                $uniqueSlug = $baseSlug . '-' . $randomString . '-' . $counter;
            }
            
            return $uniqueSlug;
        }
        
        // Helper method to get category by name
        
        private function processFormSubmission($event, $em, $services, $category, $slug)
        {
            
            // Save event images
            foreach ($event->getImages() as $image) {
                $event->addImage($image);
                $em->persist($image);
                $em->flush();
            }
            
            // Set references for event dates and tickets
            foreach ($event->getEventdates() as $eventdate) {
                $eventdate->setEvent($event);
                if (!$slug || !$eventdate->getReference()) {
                    $eventdate->setReference($services->generateReference(10));
                }
                foreach ($eventdate->getTickets() as $eventticket) {
                    $eventdate->setTitle($event->getName());
                    $eventdate->setDescription($event->getDescription());
                    $eventticket->setEventdate($eventdate);
                    if (!$slug || !$eventticket->getReference()) {
                        $eventticket->setReference($services->generateReference(10));
                    }
                }
            }
//
//        // Handle new event creation or update
//        if (!$slug) {
//            $event->setOrganizer($this->getUser()->getOrganizer());
//            $event->setReference($services->generateReference(10));
//            $this->addFlash('success', 'The event has been successfully created');
//        } else {
//            $this->addFlash('success', 'The event has been successfully updated');
//        }
            
            // Set the event category
            $event->setCategory($category);
            
            // Persist and flush changes to the database
            return $event;
        }
        
        private function processRepeatingEvent($event, $category, $commonValue, $em, $services)
        {
            $repeatEndDate = $event->getRepeatenddate();
            $copiesOfEvent = $event->getRepeatweeks();
            $repeatStartDate = $event->getEventdates()[0]->getStartdate();
            
            $event->setCommonvalue($commonValue);
            
            // Calculate the time interval between start and end dates
            $interval = $repeatStartDate->diff($repeatEndDate);

// Calculate the total number of weeks within the interval
            $totalWeeks = $interval->days / 7;

// Calculate the number of times the event repeats
            $numberOfRepeats = round($totalWeeks / $copiesOfEvent);
            
            $copiesOfEvent = (int)$numberOfRepeats;
            
            for ($index = 1; $index <= $copiesOfEvent; $index++) {
                $newEventCopy = $this->copyEvent($event);
                $newEventCopy->setCommonvalue($commonValue);
                $newEventCopy->setPublished(true);
                
                foreach ($newEventCopy->getEventdates() as $copyEventDate) {
                    $newStartDate = $this->setNewStartDateOfEventDateAndReturnThatDate($copyEventDate, $event->getRepeatweeks(), $index);
                    $copyEventDate->setStartdate($newStartDate);
                    $newEndDate = $this->setNewEndDateOfEventDateAndReturnThatDate($copyEventDate, $event->getRepeatweeks(), $index);
                    $copyEventDate->setEnddate($newEndDate);
                    $newEventCopy->addEventdate($copyEventDate);
                }
                
                $neweventtobesaved = $this->processFormSubmission($newEventCopy, $em, $services, $category, $newEventCopy->getSlug());
                $this->saveCopiedEvent($neweventtobesaved, $em);
            }
        }
        
        private function copyEvent(Event $event)
        {
            $newEvent = new Event();
            $newEvent->mapFromEvent($event);
            $newEvent->setPublished(false);
            return $newEvent;
        }
        
        // Helper method to get event choices
        
        private function setNewStartDateofeventdateAndReturnThatdate(EventDate $eventdate, int $everyxweeks, int $index)
        {
            $newstartdate = $eventdate->getStartdate();
            $addeddays = $everyxweeks * 7;
            $newstartdate->modify("+$addeddays days");
            $eventdate->setStartdate($newstartdate);
            return $eventdate->getStartdate();
        }
        
        // Helper method to initialize the event
        
        private function setNewEndDateofeventdateAndReturnThatdate(EventDate $eventdate, int $everyxweeks, int $index)
        {
            $newendtdate = $eventdate->getEnddate();
            $addeddays = $everyxweeks * 7;
            $newendtdate->modify("+$addeddays days");
            $eventdate->setEnddate($newendtdate);
            return $eventdate->getEnddate();
        }
        
        private function saveCopiedEvent(Event $event, $em)
        {
            $em->persist($event);
            $em->flush();
            
            if ($event->isPublishOnSpecificDate()) {
                // Get the selected publication date from the form
                $publishDate = $event->getPublishDate();
                
                // Check if the publication date is in the future
                $now = new DateTime(); // Get the current date and time
                if ($publishDate > $now) {
                    // Schedule the publication using Symfony's Messenger component
                    $event->setPublished(false);
                    $message = new PublishEventMessage($event->getId());
                    $message->setScheduledAt($publishDate);
                    
                    // Dispatch the message to the Messenger component
                    $this->messageBus->dispatch($message);
                } else {
                    // If the publication date is in the past or now, set the event as published
                    $event->setPublished(true);
                }
            } else {
                $event->setPublished(true);
            }
            $em->persist($event);
            $em->flush();
        }
        
        // Helper method to process form submission
        
        private function redirectToRouteAfterSubmission($authChecker)
        {
            // Implement logic to determine the route based on user roles
            if ($authChecker->isGranted('ROLE_ORGANIZER')) {
                return $this->redirectToRoute("dashboard_organizer_event");
            }
            
            if ($authChecker->isGranted('ROLE_ADMINISTRATOR')) {
                return $this->redirectToRoute("dashboard_administrator_event");
            }
            
            // Default route if no role matches
            return $this->redirectToRoute("dashboard_organizer_event");
        }
        
        private function renderEventForm($event, $form, $formsimple)
        {
            return $this->render('Dashboard/Shared/Event/add-edit.html.twig', [
                "event" => $event,
                "form" => $form->createView(),
                "formsimple" => $formsimple->createView(),
            ]);
        }
        
        // Helper method to initialize the event
        
        /**
         * @Route("/my-attendeed", name="attendeed_events")
         * @throws Exception
         */
        public function index(Request $request, PaginatorInterface $paginator, EventRepository $eventRepository, AppServices $services)
        {
            $user = $this->getUser();
            $events = $paginator->paginate($events = $eventRepository->findAttendedEventsByUser($user, "all", "all")->getQuery(), $request->query->getInt('page', 1), 12, array('wrap-queries' => true));
            return $this->render('Dashboard/Attendee/attendeed.html.twig', [
                'events' => $events
            ]);
        }
        
        // Helper method to determine the redirection route after form submission
        
        /**
         * @Route("/organizer/my-events/{slug}/edit", name="dashboard_organizer_event_edit", methods="GET|POST")
         * @throws InvalidDatabaseException
         * @throws Exception
         */
        public function editEvent(
            EventFileRepository           $eventFileRepository,
            EventRepository               $eventRepository,
            Request                       $request,
            AppServices                   $services,
                                          $slug = null,
            AuthorizationCheckerInterface $authChecker,
            CategoryRepository            $categoryRepository
        )
        {
            $organizer = $this->getUser()->getOrganizer();
            $organizerCategory = $organizer->getCategory();
            
            $categoryName = $this->mapCategoryName($organizerCategory);
            $category = $this->getCategoryByName($categoryName, $categoryRepository);
            
            if ($organizer->getCategory()) {
                $eventChoices = $this->getEventChoices($eventRepository, $organizer, $authChecker);
            } else {
                $eventChoices = $eventRepository->getAllEvents()->getQuery()->getResult();
            }
            
            $event = $this->initializeEvent($slug);
            
            if ($event == null) {
                return $services->redirectToReferer('event');
            }
            
            // In your controller action
            $entityManager = $this->getDoctrine()->getManager();
            // Specify the dynamic placeholder text based on your logic
            $form = $this->createForm(EventTypeMultiEdit::class, $event, [
                'validation_groups' => ['create', 'Default'],
                'user_choices' => $eventChoices,
            ]);
            
            $formsimple = $this->createForm(EventTypeMultiEditSimple::class, $event, [
                'validation_groups' => ['create', 'Default'],
                'user_choices' => $eventChoices,
            ]);
            
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);
            $formsimple->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                
                // Check if it's an out-of-campus event
                if ($event->getOutofcampus()) {
                    // If it's an out-of-campus event, set the venue to null
                    $event->setVenue(null);
                } else if (null === $event->getVenue()) {
                    // Set the "outofcampus" field to false
                    $event->setOutofcampus(false);
                }
                
                // Iterate through event files and associate them with the event
                foreach ($event->getFiles() as $file) {
                    $ffile = $file->getEventFile();
                    
                    if ($ffile) {
                        $newFileName = $ffile->getClientOriginalName();
                        
                        // Check if a file with the same name already exists in the event
                        $existingFile = null;
                        foreach ($event->getFiles() as $existing) {
                            if ($existing->getFileName() === $newFileName) {
                                $existingFile = $existing;
                                break; // Found a file with the same name, no need to continue searching
                            }
                        }
                        
                        if ($existingFile) {
                            // Handle the case where a file with the same name already exists
                            // You can choose to skip adding the new file, like this:
//                        continue;
                        }

//                    // If no existing file with the same name was found, add the new file
                        $newFile = new EventFile();
                        $newFile->setFileName($newFileName);
                        $newFile->setFileMimeType($ffile->getClientMimeType());
                        $newFile->setFileOriginalName($newFileName);
                        $newFile->setPosition($file->getPosition());
                        $newFile->setUpdatedAt($file->getUpdatedAt());
                        $newFile->setEventfile($file->getEventfile());
                        $event->addFile($newFile);
                    } else {
                        // Handle the case where ffile is null (no new file uploaded)
                        // Find and remove the existing file with the same name, if it exists
                        $existingFileName = $file->getFileName();
                        $existingFile = $eventFileRepository->findByFileName($existingFileName); // Implement getFileByName in your Event entity
                        
                        if ($existingFile) {
                            // Remove the existing file from the event and the database
                            $event->removeFile($existingFile);
                            $eventFileRepository->deleteById($existingFile->getId());
                            $em->remove($existingFile);
                        }
                    }
                }
                // if common value is not empty update all others too
                $event->setOrganizer($this->getUser()->getOrganizer());
                
                $this->processFormSubmission($event, $em, $services, $category, $slug);
                
                $this->informAttendeesEventhasbeenupdated($event, $services);
                
                $this->addFlash('success', 'The event has been successfully updated');
                
                return $this->redirectToRouteAfterSubmission($authChecker);
            }
            
            if ($formsimple->isSubmitted() && $formsimple->isValid()) {
                // Set references for event dates and tickets
                foreach ($event->getEventdates() as $eventdate) {
                    $eventdate->setEvent($event);
                    if (!$slug || !$eventdate->getReference()) {
                        $eventdate->setReference($services->generateReference(10));
                    }
                    foreach ($eventdate->getTickets() as $eventticket) {
                        $eventdate->setTitle($event->getName());
                        $eventdate->setDescription($event->getDescription());
                        $eventticket->setEventdate($eventdate);
                        if (!$slug || !$eventticket->getReference()) {
                            $eventticket->setReference($services->generateReference(10));
                        }
                    }
                }
                $this->processSubmittedEvent($event, $category, '', $em, $services, $authChecker);
                
                // Handle new event creation or update
                if (!$slug) {
                    $this->addFlash('success', 'The event has been successfully created');
                } else {
                    $this->addFlash('success', 'The event has been successfully updated');
                }
                
                return $this->redirectToRouteAfterSubmission($authChecker);
            }
            
            return $this->renderEventForm($event, $form, $formsimple);
        }
        
        // Helper method to render the event form
        
        private function informAttendeesEventhasbeenupdated($event, $appservices)
        {
            // get attendeees
            $attendees = $appservices->findBookedAttendeesOfEvent($event);
            foreach ($attendees as $attendee) {
                $appservices->sendmaileventhasbeenupdate($event, $attendee->getEmail());
            }
        }
        
        /**
         * @Route("/organizer/my-events/{slug}/prefill", name="dashboard_organizer_event_prefill", methods="GET|POST")
         * @throws InvalidDatabaseException
         * @throws Exception
         */
        public function prefillEvent(
            EventRepository               $eventRepository,
            Request                       $request,
            AppServices                   $services,
                                          $slug = null,
            AuthorizationCheckerInterface $authChecker,
            CategoryRepository            $categoryRepository
        )
        {
            $organizer = $this->getUser()->getOrganizer();
            $organizerCategory = $organizer->getCategory();
            
            $categoryName = $this->mapCategoryName($organizerCategory);
            $category = $this->getCategoryByName($categoryName, $categoryRepository);
            
            if ($organizer->getCategory()) {
                $eventChoices = $this->getEventChoices($eventRepository, $organizer, $authChecker);
            } else {
                $eventChoices = $eventRepository->getAllEvents()->getQuery()->getResult();
            }
            
            $event = $this->initializeEvent($slug);
            
            if ($event == null) {
                return $services->redirectToReferer('event');
            }
            $newEvent = new Event();
            $newEvent->mapFromEvent($event);
            $formType = EventTypeMulti::class;
            $form = $this->createForm($formType, $event, [
                'validation_groups' => ['create', 'Default'],
                'user_choices' => $eventChoices,
            ]);
            
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);
            
            $commonValue = $this->generateCommonValue();
            $newEvent->setCommonvalue($commonValue);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $this->processSubmittedEvent($newEvent, $category, $commonValue, $em, $services, $authChecker);
                
                if ($newEvent->isIsrepeat() && $newEvent->getRepeatsweekly()) {
                    $this->processRepeatingEvent($newEvent, $category, $commonValue, $em, $services);
                }
                
                // Handle new event creation or update
                if (!$slug) {
                    $this->addFlash('success', 'The event has been successfully created');
                } else {
                    $this->addFlash('success', 'The event has been successfully updated');
                }
                
                return $this->redirectToRouteAfterSubmission($authChecker);
            }
            
            return $this->renderEventFormprefill($event, $form);
        }
        
        private function renderEventFormprefill($event, $form)
        {
            return $this->render('Dashboard/Shared/Event/prefill.html.twig', [
                "event" => $event,
                "form" => $form->createView(),
            ]);
        }
        
        /**
         * @Route("/administrator/my-events/{slug}/delete-permanently-repeating", name="dashboard_organizer_event_delete_permanently_repeating_admin", methods="GET")
         * @throws InvalidDatabaseException
         */
        public function deleteRepeatingeventadministrator(EventRepository $eventRepository, AppServices $services, $slug)
        {
            $em = $this->getDoctrine()->getManager();
            $event = $eventRepository->findBySlug($slug);
//            if (!$event) {
            // Handle event not found
//            }
            
            // Get the common value of the current event
            $commonValue = $event->getCommonvalue();
            
            // Retrieve all events with the same common value
            $eventsWithSameCommonValue = $eventRepository->findEventsByCommonValue($commonValue);
            // Now, $eventsWithSameCommonValue contains all events with the same common value as $event
            // Delete all events with the same common value
            foreach ($eventsWithSameCommonValue as $eventToDelete) {
                if ($event->getOrderElementsQuantitySum() > 0) {
                    $this->addFlash('error', 'One or more events can not be deleted because they have one or more tickets');
                    return $services->redirectToReferer('event');
                }
                
                $em->remove($eventToDelete);
            }
            $em->remove($event);
            $em->flush();
            
            $this->addFlash('success', 'The event(s) has/have been deleted');
            return $services->redirectToReferer('event');
        }
        
        /**
         * @Route("/organizer/my-events/{slug}/delete-permanently-repeating", name="dashboard_organizer_event_delete_permanently_repeating_organizer", methods="GET")
         * @throws InvalidDatabaseException
         */
        public function deleteRepeatingeventorganizer(EventRepository $eventRepository, AppServices $services, $slug)
        {
            $em = $this->getDoctrine()->getManager();
            $event = $eventRepository->findBySlug($slug);
//            if (!$event) {
            // Handle event not found
//            }
            
            // Get the common value of the current event
            $commonValue = $event->getCommonvalue();
            
            // Retrieve all events with the same common value
            $eventsWithSameCommonValue = $eventRepository->findEventsByCommonValue($commonValue);
            // Now, $eventsWithSameCommonValue contains all events with the same common value as $event
            // Delete all events with the same common value
            
            
            foreach ($eventsWithSameCommonValue as $eventToDelete) {
//                if ($event->getOrderElementsQuantitySum() > 0) {
//                    $this->addFlash('error', 'One or more events can not be deleted because they have one or more tickets');
//                    return $services->redirectToReferer('event');
//                }
                
                $em->remove($eventToDelete);
            }
            $em->remove($event);
            $em->flush();
            
            $this->addFlash('success', 'The event(s) has/have been deleted');
            return $services->redirectToReferer('event');
        }
        
        /**
         * @Route("/organizer/my-events/{slug}/delete-permanently-repeating", name="dashboard_organizer_event_delete_permanently_repeating", methods="GET")
         * @throws InvalidDatabaseException
         */
        public function deleteRepeatingevent(EventRepository $eventRepository, AppServices $services)
        {
            $event = $eventRepository->find($eventId);

//            if (!$event) {
            // Handle event not found
//            }
            
            // Get the common value of the current event
            $commonValue = $event->getCommonvalue();
            
            // Retrieve all events with the same common value
            $eventsWithSameCommonValue = $eventRepository->findEventsByCommonValue($commonValue);
            // Now, $eventsWithSameCommonValue contains all events with the same common value as $event
            
            // Delete all events with the same common value
            foreach ($eventsWithSameCommonValue as $eventToDelete) {
                if ($event->getOrderElementsQuantitySum() > 0) {
                    $this->addFlash('error', 'One or more events can not be deleted because they have one or more tickets');
                    return $services->redirectToReferer('event');
                }
                $em->remove($eventToDelete);
            }
            
            $this->addFlash('success', 'The event(s) has/have been deleted');
            return $services->redirectToReferer('event');
        }
        
        /**
         * @Route("/administrator/my-events/{slug}/delete-permanently-admin", name="dashboard_organizer_event_delete_permanently_admin", methods="GET")
         * @throws InvalidDatabaseException|Exception
         * @throws Exception
         */
        public function deleteforadmin(AppServices $services, $slug, AuthorizationCheckerInterface $authChecker)
        {
            $organizer = "all";
            if ($authChecker->isGranted('ROLE_ORGANIZER')) {
                $organizer = $this->getUser()->getOrganizer()->getSlug();
            }
            
            $event = $services->getEvents(array("slug" => $slug, "published" => "all", "elapsed" => "all", "organizer" => $organizer, "organizerEnabled" => "all"))[0];
            if (!$event) {
                $this->addFlash('error', 'The event can not be found');
                return $services->redirectToReferer('event');
            }
            
            if ($event->getDeletedAt() !== null) {
                $this->addFlash('error', 'The event has been deleted permanently');
            } else {
                $this->addFlash('success', 'The event has been deleted');
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
            return $services->redirectToReferer('event');
        }
        
        /**
         * @Route("/administrator/manage-events/{slug}/delete-permanently", name="dashboard_administrator_event_delete_permanently", methods="GET")
         * @Route("/administrator/manage-events/{slug}/delete", name="dashboard_administrator_event_delete", methods="GET")
         * @Route("/organizer/my-events/{slug}/delete-permanently", name="dashboard_organizer_event_delete_permanently", methods="GET")
         * @Route("/organizer/my-events/{slug}/delete", name="dashboard_organizer_event_delete", methods="GET")
         * @throws InvalidDatabaseException|Exception
         */
        public function delete(AppServices $services, OrderRepository $orderRepository, $slug, AuthorizationCheckerInterface $authChecker)
        {
            $organizer = "all";
            if ($authChecker->isGranted('ROLE_ORGANIZER')) {
                $organizer = $this->getUser()->getOrganizer()->getSlug();
            }
            
            $event = $services->getEvents(array("slug" => $slug, "published" => "all", "elapsed" => "all", "organizer" => $organizer, "organizerEnabled" => "all"))[0];
            if (!$event) {
                $this->addFlash('error', 'The event can not be found');
                return $services->redirectToReferer('event');
            }
            
            if ($event->getDeletedAt() !== null) {
                $this->addFlash('error', 'The event has been deleted permanently');
            } else {
                $this->addFlash('success', 'The event has been deleted');
            }
            
            $em = $this->getDoctrine()->getManager();
            $orders = $orderRepository->findByEventId($event->getId());
            foreach ($orders as $order) {
                $em->remove($order);
                $em->flush();
            }
            $em->remove($event);
            $em->flush();
//            $this->informAttendeesEventhasbeencancelled($event, $services);
            return $services->redirectToReferer('event');
        }
        
        /**
         * @Route("/administrator/manage-events/{slug}/restore", name="dashboard_administrator_event_restore", methods="GET")
         * @throws InvalidDatabaseException|NonUniqueResultException
         */
        public function restore($slug, AppServices $services)
        {
            
            $event = $services->getEvents(array("slug" => $slug, "published" => "all", "elapsed" => "all", "organizer" => "all", "organizerEnabled" => "all"))[0];
            if (!$event) {
                $this->addFlash('error', 'The event can not be found');
                return $services->redirectToReferer('event');
            }
            $event->setDeletedAt(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'The event has been succesfully restored');
            
            return $services->redirectToReferer('event');
        }
        
        /**
         * @Route("/organizer/my-events/{slug}/publish", name="dashboard_organizer_event_publish", methods="GET")
         * @Route("/organizer/my-events/{slug}/draft", name="dashboard_organizer_event_draft", methods="GET")
         * @throws InvalidDatabaseException|NonUniqueResultException|Exception|InvalidArgumentException
         */
        public function showhide(AppServices  $services, $slug, AuthorizationCheckerInterface $authChecker,
                                 Swift_Mailer $mailer, Twig_Environment $templating)
        {
            
            $organizer = "all";
            if ($authChecker->isGranted('ROLE_ORGANIZER')) {
                $organizer = $this->getUser()->getOrganizer()->getSlug();
            }
            $event = $services->getEvents(array("slug" => $slug, "organizer" => $organizer))[0];
            if (!$event) {
                $this->addFlash('error', 'The event can not be found');
                return $services->redirectToReferer('event');
            }
            if ($event->getPublished() === true) {
                $event->setPublished(false);
                $this->addFlash('success', 'The event has been unpublished and will not be included in the search results');
            } else {
                $event->setPublished(true);
                // send to followers
                $followerslist = $this->getUser()->getOrganizer()->getFollowedBy();
                $filteredFollowers = [];
                
                foreach ($followerslist as $follower) {
                    if ($follower->getId() !== $this->getUser()->getId()) {
                        $filteredFollowers[] = $follower;
                    }
                }
                
                $emaildata = [
                    $title = $event->getName(),
                    $startdate = $event->getEventdates()[0]->getStartdate()->format('Y-m-d H:i:s'),
                    $link = "https://universeacg.xyz/event/" . $event->getSlug(),
                ];
                
                $message = "The creator you are following has just created a new event: " . $event->getName();
                
                foreach ($filteredFollowers as $follower) {
                    $email = (new Swift_Message($services->getSetting('website_name') . ' - ' . $event->getName()))
                        ->setFrom($services->getSetting('no_reply_email'))
                        ->setTo($follower->getEmail())
                        ->setBody(
                            $templating->render('Dashboard/Shared/Order/new-event-email.html.twig', ['data' => $emaildata, 'message' => $message]), 'text/html');
                    
                    $this->notificationService->createNotification($this->getUser()->getId(), $message, $email->getSubject());
                    
                    try {
                        $result = $mailer->send($email);
                    } catch (Exception $e) {
                        $this->addFlash('danger', "The email to the followers could not be sent");
                    }
                }
                $this->addFlash('success', 'The event has been published and will show up in the search results');
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            return $services->redirectToReferer('event');
        }
        
        /**
         * @Route("/administrator/manage-events/{slug}/details", name="dashboard_administrator_event_details", methods="GET", condition="request.isXmlHttpRequest()")
         * @Route("/organizer/my-events/{slug}/details", name="dashboard_organizer_event_details", methods="GET", condition="request.isXmlHttpRequest()")
         * @throws InvalidDatabaseException|NonUniqueResultException
         */
        public function details(AppServices $services, $slug, AuthorizationCheckerInterface $authChecker)
        {
            
            $organizer = "all";
            if ($authChecker->isGranted('ROLE_ORGANIZER')) {
                $organizer = $this->getUser()->getOrganizer()->getSlug();
            }
            
            $event = $services->getEvents(array("slug" => $slug, "published" => "all", "elapsed" => "all", "organizer" => $organizer, "organizerEnabled" => "all"))[0];
            if (!$event) {
                return new Response('The event can not be found');
            }
            return $this->render('Dashboard/Shared/Event/details.html.twig', [
                'event' => $event,
            ]);
        }
        
        private function findClosestFutureStartDate($event)
        {
            $now = new DateTime();
            $closestStartDate = null;
            
            foreach ($event->getEventDates() as $eventDate) {
                $startDate = $eventDate->getStartDate();
                
                if ($startDate > $now && (!$closestStartDate || $startDate < $closestStartDate)) {
                    $closestStartDate = $startDate;
                }
            }
            
            return $closestStartDate;
        }
        
        private function informAttendeesEventhasbeencancelled($event, $appservices)
        {
            // get attendeees
            $attendees = $appservices->findBookedAttendeesOfEvent($event);
            foreach ($attendees as $attendee) {
                $appservices->sendmaileventhasbeencancelled($event, $attendee->getEmail());
            }
        }
    }