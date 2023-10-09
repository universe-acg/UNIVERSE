<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Front;
    
    use App\Entity\Organizer;
    use App\Repository\OrganizerRepository;
    use App\Repository\UserRepository;
    use App\Service\AppServices;
    use App\Service\ManagementClubsSocietiesService;
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
        public function organizer(ManagementClubsSocietiesService $managementClubsSocietiesService, $slug, AppServices $services, UserRepository $userRepository): Response
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
                    'pastevents' => $pastevents, 'news' => $news, 'managementClubsSocietiesService' => $managementClubsSocietiesService]
            );
        }
        
        /**
         * @Route("/creators", name="creators", methods={"GET", "POST"})
         */
        public function creators(
            OrganizerRepository $organizerRepository,
            Request             $request,
            AppServices         $appServices,
            PaginatorInterface  $paginator
        ): Response
        {
            $category = $request->get('category') ?? "all";
            $keyword = $request->get('keyword') ?? "all";
            $level = $request->get('level') ?? "all";
            $sortedby = $request->get('sortedby') ?? "all";
            
            if ($category != "all") {
                $category = $this->normalizeCategory($category);
            }
            
            $criteria = [
                "category" => $category,
                "keyword" => $keyword,
                "level" => $level,
                'sortedby' => $sortedby
            ];
            
            $organizers = $appServices->getCreators($criteria)->getQuery()->getResult();
            
            foreach ($organizers as $organizer) {
                $organizer->setNewscount(count($this->getDoctrine()->getRepository(Organizer::class)->findNewsByUser($organizer->getUser())));
                $organizer->setEventscount(count($appServices->getEvents(array("elapsed" => "all", "organizer" => $organizer->getSlug()))->getQuery()->getResult()));
                $organizer->setFollowerscount(count($organizer->getFollowedby()));
            }
            
            // remove all those offices and orgs if level is set
            if ($level !== 'all') {
                $categoriesToExclude = ["Office", "Organization"];

// Filter the organizers array to remove those with excluded categories
                $organizers = array_filter($organizers, static function ($organizer) use ($categoriesToExclude) {
                    return !in_array($organizer->getCategory(), $categoriesToExclude, true);
                });
            }
            
            if ($sortedby == 'all') {
                $organizers = $this->sortByPoints($organizers);
            } elseif ($sortedby == 'mostevents') {
                $organizers = $this->sortByMostEvents($organizers);
            } elseif ($sortedby == 'mostnews') {
                $organizers = $this->sortByMostNews($organizers);
            } elseif ($sortedby == 'mostfollowers') {
                $organizers = $this->sortByMostFollowers($organizers);
            }
            
            $pagination = $paginator->paginate(
                $organizers,
                $request->query->getInt('page', 1),
                40
            );
            
            return $this->render('Front/Organizer/organizers.html.twig', [
                'creators' => $pagination,
                'selectedCategory' => $category,
                'keyword' => $keyword,
                'level' => $level
            ]);
        }
        
        /**
         * Normalizes a category value.
         *
         * @param string $category
         *
         * @return string
         */
        private function normalizeCategory(string $category): string
        {
            $categoryMappings = [
                'Clubs' => 'Club',
                'Societies' => 'Society',
                'Offices' => 'Office',
                'Organizations' => 'Organization',
            ];
            
            // Use the mapping or return the original value if no mapping exists
            return $categoryMappings[$category] ?? $category;
        }
        
        private function sortByPoints($organizers)
        {
            $clubsAndSocieties = [];
            $otherOrganizers = [];
            
            // Separate clubs and societies from other organizers
            foreach ($organizers as $organizer) {
                if ($organizer->getCategory() === 'Club' || $organizer->getCategory() === 'Society') {
                    $clubsAndSocieties[] = $organizer;
                } else {
                    $otherOrganizers[] = $organizer;
                }
            }
            
            // Custom sorting function to sort clubs and societies by points in descending order
            usort($clubsAndSocieties, static function ($a, $b) {
                $pointsA = $a->getUser()->getPoints(); // Replace with the actual method to get points
                $pointsB = $b->getUser()->getPoints(); // Replace with the actual method to get points
                
                // Sort in descending order (higher points first)
                if ($pointsA > $pointsB) {
                    return -1;
                }
                
                if ($pointsA < $pointsB) {
                    return 1;
                }
                
                return 0;
            });
            
            // Shuffle other organizers for random order
            shuffle($otherOrganizers);
            // Combine clubs and societies with other organizers
            return array_merge($clubsAndSocieties, $otherOrganizers);
        }
        
        /**
         * Sort organizers by the count of events in descending order.
         *
         * @param Organizer[] $organizers
         * @return Organizer[]
         */
        private function sortByMostEvents(array $organizers): array
        {
            usort($organizers, static function ($a, $b) {
                return $b->getEventscount() <=> $a->getEventscount();
            });
            
            return $organizers;
        }
        
        /**
         * Sort organizers by the count of news in descending order.
         *
         * @param Organizer[] $organizers
         * @return Organizer[]
         */
        private function sortByMostNews(array $organizers): array
        {
            usort($organizers, static function ($a, $b) {
                return $b->getNewscount() <=> $a->getNewscount();
            });
            
            return $organizers;
        }
        
        /**
         * Sort organizers by the count of followers in descending order.
         *
         * @param Organizer[] $organizers
         * @return Organizer[]
         */
        private function sortByMostFollowers(array $organizers): array
        {
            usort($organizers, static function ($a, $b) {
                return $b->getFollowerscount() <=> $a->getFollowerscount();
            });
            
            return $organizers;
        }
        
        /**
         * Calculate the count of news for an organizer.
         *
         * @param Organizer $organizer
         * @return int
         */
        private function getNewsCountForOrganizer(Organizer $organizer): int
        {
            return count($this->getDoctrine()->getRepository(Organizer::class)->findNewsByUser($organizer->getUser()));
        }
        
        /**
         * Shuffles an array of organizers.
         *
         * @param array $organizers
         *
         * @return array
         */
        private function shuffleOrganizers(array $organizers): array
        {
            shuffle($organizers);
            return $organizers;
        }
    }
