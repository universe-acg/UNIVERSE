<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;
    
    use App\Entity\HomepageHeroSettings;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class MainController extends AbstractController
    {
        
        /**
         * @Route("/", name="homepage")
         */
        public function homepage(): Response
        {
            
            $herosettings = $this->getDoctrine()->getManager()->getRepository(HomepageHeroSettings::class)->find(1);
            
            return $this->render('Front/Main/homepage.html.twig', [
                'herosettings' => $herosettings
            ]);
        }
        
    }
