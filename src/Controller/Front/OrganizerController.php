<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Front;
    
    use App\Entity\Organizer;
    use App\Repository\OrganizerRepository;
    use App\Repository\UserRepository;
    use App\Service\AppServices;
    use DateTime;
    use Knp\Component\Pager\PaginatorInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class OrganizerController extends AbstractController
    {
        
        /**
         * @Route("/organizer/{slug}", name="organizer")
         */
        public function organizer($slug, AppServices $services, UserRepository $userRepository): Response
        {
            $user = $userRepository->getByUsername($slug);
            
            if (!$user || !$user->getOrganizer()) {
                $this->addFlash('error', 'The creator could not be found');
                return $services->redirectToReferer("events");
            }
            
            $em = $this->getDoctrine()->getManager();
            $user->getOrganizer()->viewed();
            $em->flush();
            
            $org = $user->getOrganizer();
            $pastevents = $services->getEvents(array('elapsed' => true, 'organizer' => $org->getSlug()))->getQuery()->getResult();
            // Get the current date and time
            $currentDateTime = new DateTime();
            
            // Create an array to store past events with all event dates in the past
            $filteredPastEvents = [];
            
            foreach ($pastevents as $event) {
                $eventDatesInPast = true;
                
                if (!empty($event->getEventdates())) {
                    foreach ($event->getEventdates() as $eventDate) {
                        $startDate = $eventDate->getEnddate();
                        
                        // Check if the event date is in the past
                        if ($startDate > $currentDateTime) {
                            $eventDatesInPast = false;
                            break; // If any event date is not in the past, break out of the loop
                        }
                    }
                }
                
                // Include the event only if all event dates are in the past
                if ($eventDatesInPast) {
                    $filteredPastEvents[] = $event;
                }
            }
            
            $currentevnts = $services->getEvents(array('elapsed' => false, 'organizer' => $org->getSlug()))->getQuery()->getResult();
            foreach ($currentevnts as &$event) {
                if (count($event->getEventdates()) == 0) {
                    // If there are no event dates, continue to the next event
                    continue;
                }
                
                $maxStartDate = null;
                $maxStartDateEventDate = null;
                
                foreach ($event->getEventdates() as $eventDate) {
                    $startDate = $eventDate->getStartdate();
                    
                    if (!$maxStartDate || $startDate > $maxStartDate) {
                        $maxStartDate = $startDate;
                        $maxStartDateEventDate = $eventDate;
                    }
                }
                
                // Replace the event's eventdates array with an array containing only the one with the max start date
                $event->setEventdates([$maxStartDateEventDate]);
            }
            
            $news = $this->getDoctrine()->getRepository(Organizer::class)->findNewsByUser($user);
            
            
            return $this->render('Front/Organizer/profile.html.twig', ['organizer' => $org, 'currentevnts' => $currentevnts,
                    'pastevents' => $filteredPastEvents, 'news' => $news]
            );
        }
        
        /**
         * @Route("/creators", name="creators", methods="GET|POST")
         */
        public function creators(UserRepository $userRepository, OrganizerRepository $organizerRepository, Request $request, AppServices $services, PaginatorInterface $paginator): Response
        {
            // Get the category and keyword from the request
            $category = $request->request->get('category');
            $keyword = $request->request->get('keyword');
            if ($category == "Clubs") {
                $category = "Club";
            } elseif ($category == "Societies") {
                $category = "Society";
            } elseif ($category == "Offices") {
                $category = "Office";
            } elseif ($category == "Organizations") {
                $category = "Organization";
            } else {
                $category = "";
            }
            // Execute the query and fetch the results as an array of entities
            $organizers = $organizerRepository->searchByKeywordAndCategory($keyword, $category);
            
            $pagination = $paginator->paginate(
                $organizers,               // Use the filtered $organizers array
                $request->query->getInt('page', 1),
                20
            );
            
            return $this->render('Front/Organizer/organizers.html.twig', [
                'creators' => $pagination,
                'selectedCategory' => $category,
                'keyword' => $keyword,
            ]);
        }
    }
