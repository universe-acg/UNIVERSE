<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Front;
    
    use App\Entity\HomepageHeroSettings;
    use App\Repository\EventRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class MainController extends AbstractController
    {
        
        /**
         * @Route("/", name="homepage")
         */
        public function homepage(EventRepository $eventRepository): Response
        {
            $herosettings = $this->getDoctrine()->getManager()->getRepository(HomepageHeroSettings::class)->find(1);
            $entityManager = $this->getDoctrine()->getManager();
            
            // Query the Event entity to fetch the last 8 published events
            $events = $eventRepository->findUpcomingEvents();
            
            return $this->render('Front/Main/homepage.html.twig', [
                'herosettings' => $herosettings,
                'events' => $events,
            ]);
        }
    }
