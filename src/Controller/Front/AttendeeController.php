<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Front;
    
    use App\Repository\BlogPostRepository;
    use App\Repository\CommentRepository;
    use App\Repository\EventRepository;
    use App\Repository\UserRepository;
    use App\Service\AppServices;
    use App\Service\ManagementClubsSocietiesService;
    use Knp\Component\Pager\PaginatorInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class AttendeeController extends Controller
    {
        private $eventRepository;
        private $userRepository;
        private $blogPostRepository;
        
        private $appServices;
        
        private $paginator;
        
        private $commentRepository;
        
        public function __construct(PaginatorInterface $paginator, AppServices $appServices, EventRepository $eventRepository, UserRepository $userRepository, BlogPostRepository $blogPostRepository, CommentRepository $commentRepository)
        {
            $this->eventRepository = $eventRepository;
            $this->userRepository = $userRepository;
            $this->blogPostRepository = $blogPostRepository;
            $this->appServices = $appServices;
            $this->paginator = $paginator;
            $this->commentRepository = $commentRepository;
        }
        
        /**
         * @Route("/attendee-profile/{slug}", name="profile_attendee", methods="GET")
         */
        public function viewprofile(Request $request, ManagementClubsSocietiesService $managementClubsSocietiesService, PaginatorInterface $paginator, $slug, AppServices $services): Response
        {
            $referer = $request->headers->get('referer');
            $attendee = $this->appServices->getUsers(array('slug' => $slug))->getQuery()->getOneOrNullresult();
            if (!$attendee) {
                $this->addFlash('error', 'The user could not be found');
                return $services->redirectToReferer($referer);
            }
            $blogposts = $this->blogPostRepository->getBlogPostsByUserId($attendee)->getQuery()->getResult();
            $level = $attendee->stringifypointslevelAttendee();
            
            // favorites sort by last created
            
            if ($attendee->isShowfavs()) {
                $favoriteEvents = $services->getEvents(array("addedtofavoritesby" => $attendee))->getQuery()->getResult();
            } else {
                $favoriteEvents = [];
            }
            
            // comments sort by last written
            if ($attendee->isShowcomments()) {
                $comments = $this->commentRepository->findByAuthor($attendee->getId())->getQuery()->getResult();
            } else {
                $comments = [];
            }
            
            // attendeed sort by last created
            if ($attendee->isShowattendeed()) {
                $attendedEvents = $this->eventRepository->findAttendedEventsByUser($attendee, "all", "all")->getQuery()->getResult();
            } else {
                $attendedEvents = [];
            }
            
            $posts = $this->blogPostRepository->getBlogPostsByUserId($attendee->getId())->getQuery()->getResult();
            
            return $this->render('Dashboard/Attendee/profile.html.twig', [
                'managementClubsSocietiesService' => $managementClubsSocietiesService,
                'attendee' => $attendee,
                'blogposts' => $blogposts,
                'level' => $level,
                'favoriteEvents' => $favoriteEvents,
                'attendedEvents' => $attendedEvents,
                'posts' => $posts,
                'comments' => $comments,
            ]);
        }
    }