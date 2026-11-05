<?php
namespace App\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
final class UserManager
{
    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher) {}
    public function createUser(): User { return new User(); }
    public function updateUser(User $user, bool $flush = true): void {
        if ($user->getPlainPassword()) { $user->setPassword($this->hasher->hashPassword($user, $user->getPlainPassword())); $user->eraseCredentials(); }
        $this->em->persist($user);
        if ($flush) { $this->em->flush(); }
    }
    public function findUserBy(array $criteria): ?User { return $this->em->getRepository(User::class)->findOneBy($criteria); }
    public function findUserByEmail(string $email): ?User { return $this->findUserBy(['emailCanonical'=>mb_strtolower($email)]); }
}
