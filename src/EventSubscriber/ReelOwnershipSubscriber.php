<?php
namespace App\EventSubscriber;
use App\Controller\Dashboard\Shared\ReelsController;
use App\Entity\Reel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
#[AsEventListener(event:'kernel.controller')]
final class ReelOwnershipSubscriber
{
    public function __construct(private EntityManagerInterface $em,private Security $security) {}
    public function __invoke(ControllerEvent $event): void
    {
        $controller=$event->getController(); if (!is_array($controller) || !$controller[0] instanceof ReelsController || $this->security->isGranted('ROLE_ADMINISTRATOR')) return;
        $slug=$event->getRequest()->attributes->get('slug'); if (!$slug) return;
        $reel=$this->em->getRepository(Reel::class)->findOneBy(['slug'=>$slug]);
        if ($reel && $reel->getUser()?->getId()!==$this->security->getUser()?->getId()) throw new AccessDeniedHttpException();
    }
}
