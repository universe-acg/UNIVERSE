<?php
namespace App\Controller\Front;

use App\Controller\BaseController;
use App\Entity\{Event, Organizer, User};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends BaseController
{
    #[Route('/', name: 'homepage')]
    public function homepage(EntityManagerInterface $em): Response
    {
        $students = $em->createQueryBuilder()->select('COUNT(u.id)')->from(User::class, 'u')
            ->where('u.enabled = true')->andWhere('u.roles LIKE :role')->setParameter('role', '%ROLE_ATTENDEE%')->getQuery()->getSingleScalarResult();
        $events = $em->createQueryBuilder()->select('e', 'MIN(d.startdate) AS HIDDEN nextDate')->from(Event::class, 'e')
            ->join('e.eventdates', 'd')->where('e.published = true')->andWhere('d.startdate >= :now')
            ->setParameter('now', new \DateTimeImmutable('now', new \DateTimeZone('Europe/Athens')))
            ->groupBy('e.id')->orderBy('nextDate', 'ASC')->setMaxResults(3)->getQuery()->getResult();
        return $this->render('Front/Main/homepage.html.twig', [
            'events' => $events,
            'stats' => [
                'Published events' => $em->getRepository(Event::class)->count(['published' => true]),
                'Clubs' => $em->getRepository(Organizer::class)->count(['category' => 'Club']),
                'Student profiles' => (int)$students,
                'Creators' => $em->getRepository(Organizer::class)->count([]),
            ],
        ]);
    }
}
