<?php
namespace App\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
final class NativeUserProvider implements UserProviderInterface
{
    public function __construct(private EntityManagerInterface $em) {}
    public function loadUserByIdentifier(string $identifier): UserInterface {
        $user=$this->em->getRepository(User::class)->createQueryBuilder('u')->where('u.usernameCanonical = :identifier OR u.emailCanonical = :identifier')->setParameter('identifier',mb_strtolower(trim($identifier)))->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if(!$user){$error=new UserNotFoundException();$error->setUserIdentifier($identifier);throw $error;}
        return $user;
    }
    public function refreshUser(UserInterface $user): UserInterface {
        if(!$user instanceof User)throw new UnsupportedUserException();
        return $this->em->find(User::class,$user->getId())??throw new UserNotFoundException();
    }
    public function supportsClass(string $class): bool { return is_a($class,User::class,true); }
}
