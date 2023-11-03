<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\AthleticBooking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AthleticBookingRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AthleticBooking::class);
    }
}
