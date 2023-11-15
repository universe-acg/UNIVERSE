<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\ListingCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ListingCategoryRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListingCategory::class);
    }

    // Add your custom method to find a category by name
    public function findByName($name)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCategories($hidden, $keyword, $slug, $limit)
    {
        $qb = $this->createQueryBuilder("c");
        $qb->select("DISTINCT c");

        if ($hidden !== "all") {
            $qb->andWhere("c.hidden = :hidden")->setParameter("hidden", $hidden);
        }

        if ($keyword !== "all") {
            $qb->andWhere("c.name LIKE :keyword or :keyword LIKE c.name")->setParameter("keyword", "%" . $keyword . "%");
        }

        if ($slug !== "all") {
            $qb->andWhere("c.slug = :slug")->setParameter("slug", $slug);
        }

        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }

        $qb->orderBy("c.createdAt", 'DESC');

        return $qb;
    }

    // Add a method to retrieve a category by its ID
    public function getCategoryById($categoryId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $categoryId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByUserId($userId)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.user', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function getAvailableCategories()
    {
        $qb = $this->createQueryBuilder("c");

        $qb->select("DISTINCT c");
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->isNull('c.hidden'),
            $qb->expr()->eq('c.hidden', 0)
        ));

        $qb->orderBy('c.createdAt', 'DESC');

        return $qb;
    }
}
