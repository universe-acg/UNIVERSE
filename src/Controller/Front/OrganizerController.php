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
            $pastevents = $services->getEvents(array('organizer' => $org->getSlug()))->getQuery()->getResult();
            $currentDateTime = new DateTime(); // Get the current date and time

// Filter the $pastevents array to keep only events with enddate smaller than the current time
            $pastevents = array_filter($pastevents, static function ($event) use ($currentDateTime) {
                foreach ($event->getEventdates() as $eventDate) {
                    if ($eventDate->getEnddate() < $currentDateTime) {
                        return true; // Keep the event if any event date's enddate is smaller than the current time
                    }
                }
                return false; // Exclude the event if none of its event dates meet the condition
            });
            
            $currentevnts = $services->getEvents(array('organizer' => $org->getSlug()))->getQuery()->getResult();
            
            $currentevnts = array_filter($currentevnts, static function ($event) use ($currentDateTime) {
                foreach ($event->getEventdates() as $eventDate) {
                    if ($eventDate->getEnddate() > $currentDateTime) {
                        return true; // Keep the event if any event date's enddate is smaller than the current time
                    }
                }
                return false; // Exclude the event if none of its event dates meet the condition
            });
            
            
            $news = $this->getDoctrine()->getRepository(Organizer::class)->findNewsByUser($user);
            
            
            return $this->render('Front/Organizer/profile.html.twig', ['organizer' => $org, 'currentevnts' => $currentevnts,
                    'pastevents' => $pastevents, 'news' => $news]
            );
        }
        
        /**
         * @Route("/creators", name="creators", methods="GET|POST")
         */
        public function creators(OrganizerRepository $organizerRepository, Request $request, PaginatorInterface $paginator): Response
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
            shuffle($organizers);
            
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
