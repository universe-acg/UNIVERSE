<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Entity\Event;
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
        $entityManager = $this->getDoctrine()->getManager();

        // Query the Event entity to fetch the last 8 published events
        $events = $entityManager->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->where('e.published = :published')
            ->setParameter('published', true)
            ->orderBy('e.createdAt', 'DESC') // You can change the order as needed
            ->setMaxResults(8) // Limit to 8 events
            ->getQuery()
            ->getResult();

        return $this->render('Front/Main/homepage.html.twig', [
            'herosettings' => $herosettings,
            'events' => $events,
        ]);
    }
}
