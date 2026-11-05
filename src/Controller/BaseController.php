<?php
namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
abstract class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array {
        return parent::getSubscribedServices() + ['doctrine' => ManagerRegistry::class, 'security.token_storage'=>\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface::class];
    }
    protected function getDoctrine(): ManagerRegistry { return $this->container->get('doctrine'); }
    protected function get(string $id): mixed { return $this->container->get($id); }
}
