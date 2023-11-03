<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Amenity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AmenityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Amenity::class);
    }

    public function getAmenities(
        $hidden = false,
        $keyword = 'all',
        $slug = 'all',
        $limit = 'all'
    )
    {
        $qb = $this->createQueryBuilder('a');

//        if ($hidden !== false) {
//            $qb->andWhere('a.hidden = :hidden')
//                ->setParameter('hidden', $hidden);
//        }
//
//        if ($keyword !== 'all') {
//            // Add conditions for the keyword search, e.g., for 'name' or other fields
//            $keyword = '%' . $keyword . '%';
//            $qb->andWhere(
//                $qb->expr()->orX(
//                    $qb->expr()->like('a.name', ':keyword'),
//                )
//            )->setParameter('keyword', $keyword);
//        }
//
//        if ($slug !== 'all') {
//            $qb->andWhere('a.slug = :slug')
//                ->setParameter('slug', $slug);
//        }
//
//        if ($limit !== 'all') {
//            $qb->setMaxResults($limit);
//        }

        return $qb->getQuery()->getResult();
    }

    public function getAmenitiessql(
        $hidden = false,
        $keyword = 'all',
        $slug = 'all',
        $limit = 'all'
    )
    {
        $qb = $this->createQueryBuilder('a');

//        if ($hidden !== false) {
//            $qb->andWhere('a.hidden = :hidden')
//                ->setParameter('hidden', $hidden);
//        }
//
//        if ($keyword !== 'all') {
//            // Add conditions for the keyword search, e.g., for 'name' or other fields
//            $keyword = '%' . $keyword . '%';
//            $qb->andWhere(
//                $qb->expr()->orX(
//                    $qb->expr()->like('a.name', ':keyword'),
//                )
//            )->setParameter('keyword', $keyword);
//        }
//
//        if ($slug !== 'all') {
//            $qb->andWhere('a.slug = :slug')
//                ->setParameter('slug', $slug);
//        }
//
//        if ($limit !== 'all') {
//            $qb->setMaxResults($limit);
//        }

        return $qb;
    }

}