<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\EventFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventFile[]    findAll()
 * @method EventFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventFile::class);
    }

    public function findByFileName($fileName)
    {
        return $this->createQueryBuilder('ef')
            ->where('ef.fileName = :fileName')
            ->setParameter('fileName', $fileName)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySomeField($value): ?EventFile
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteById($id)
    {
        $eventFile = $this->find($id);

        if ($eventFile) {
            $entityManager = $this->getEntityManager();
            $entityManager->remove($eventFile);
            $entityManager->flush();
        }
    }
}
