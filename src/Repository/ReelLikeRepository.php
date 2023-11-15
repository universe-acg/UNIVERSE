<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\ReelLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReelLikeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReelLike::class);
    }

    /**
     * Get likes of a user
     *
     * @param int $userId
     * @return ReelLike[]
     */
    public function getLikesOfUser(int $userId): array
    {
        return $this->createQueryBuilder('rl')
            ->andWhere('rl.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the total number of likes for a specific reel
     *
     * @param int $reelId
     * @return int
     */
    public function getLikeCountForReel(int $reelId): int
    {
        return $this->createQueryBuilder('rl')
            ->select('COUNT(rl.id)')
            ->andWhere('rl.reel = :reelId')
            ->setParameter('reelId', $reelId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get the users who liked a specific reel
     *
     * @param int $reelId
     * @return array
     */
    public function getUsersWhoLikedReel(int $reelId): array
    {
        return $this->createQueryBuilder('rl')
            ->select('u')
            ->leftJoin('rl.user', 'u')
            ->andWhere('rl.reel = :reelId')
            ->setParameter('reelId', $reelId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get like of a user for a specific reel
     *
     * @param int $userId
     * @param int $reelId
     * @return ReelLike|null
     */
    public function getLikeOfUserForReel(int $userId, int $reelId): ?ReelLike
    {
        return $this->createQueryBuilder('rl')
            ->andWhere('rl.user = :userId')
            ->andWhere('rl.reel = :reelId')
            ->setParameter('userId', $userId)
            ->setParameter('reelId', $reelId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
