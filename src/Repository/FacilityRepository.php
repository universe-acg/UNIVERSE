<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\FacilityEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FacilityRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacilityEntity::class);
    }
}
