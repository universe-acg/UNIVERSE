<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

// Add the findLastFiveNotifications method
    public function findLastFiveNotificationsByUser($userId)
    {
        return $this->createQueryBuilder('n')
            ->where('n.userid = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function countUnreadNotificationsByUser($userId)
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.userid = :userId')
            ->andWhere('n.read = :unread')
            ->setParameter('userId', $userId)
            ->setParameter('unread', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function markAllNotificationsAsReadByUser($userId)
    {
        $queryBuilder = $this->createQueryBuilder('n')
            ->update(Notification::class, 'n')
            ->set('n.read', true)
            ->where('n.userid = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        $queryBuilder->execute();
    }

    public function markNotificationAsReadByUser($userId, $notificationId)
    {
        $notification = $this->findNotificationsByUserAndId($userId, $notificationId);
        if ($notification) {
            $notification->setRead(true);
            $this->_em->flush();
        }
    }

    public function findNotificationsByUserAndId($userId, $notificationId)
    {
        return $this->createQueryBuilder('n')
            ->where('n.userid = :userId')
            ->andWhere('n.id = :notificationId')
            ->setParameter('userId', $userId)
            ->setParameter('notificationId', $notificationId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNotificationsByUser($userId)
    {
        return $this->createQueryBuilder('n')
            ->where('n.userid = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findUnreadNotificationsByUser($userId)
    {
        return $this->createQueryBuilder('n')
            ->where('n.userid = :userId')
            ->andWhere('n.read = :isRead')
            ->setParameter('userId', $userId)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getResult();
    }
}
