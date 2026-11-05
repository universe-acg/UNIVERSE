<?php
namespace App\Service;
use App\Repository\UserRepository;
final class LeaderboardsService
{
    public function __construct(private UserRepository $users) {}
    public function getAllAttendees(): array { return $this->ranked('ROLE_ATTENDEE'); }
    public function getAllCreators(): array { return $this->ranked('ROLE_ORGANIZER'); }
    private function ranked(string $role): array
    {
        $qb=$this->users->createQueryBuilder('u')->where('u.enabled=true AND u.deletedAt IS NULL')->andWhere('u.roles LIKE :role')->setParameter('role','%'.$role.'%')->orderBy('u.points','DESC')->addOrderBy('u.username','ASC');
        if ($role==='ROLE_ORGANIZER') $qb->join('u.organizer','o')->addSelect('o');
        return $qb->getQuery()->getResult();
    }
}
