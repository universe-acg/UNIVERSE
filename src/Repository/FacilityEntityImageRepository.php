<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\FacilityEntityImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FacilityEntityImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacilityEntityImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacilityEntityImage[]    findAll()
 * @method FacilityEntityImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacilityEntityImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacilityEntityImage::class);
    }

//    public function findByExampleField($value)
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult();
//    }
//
//    public function findOneBySomeField($value): ?FacilityEntityImage
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult();
//    }
}
