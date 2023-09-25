<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function getMenus($slug)
    {
        $qb = $this->createQueryBuilder("m");
        $qb->select("m");
        if ($slug !== "all") {
            $qb->andWhere("m.slug = :slug")->setParameter("slug", $slug);
        }
        return $qb;
    }
}
