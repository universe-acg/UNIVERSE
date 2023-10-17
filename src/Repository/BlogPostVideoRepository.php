<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Repository;

use App\Entity\BlogPostVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BlogPostVideoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPostVideo::class);
    }

    public function getBlogPostVideos($selecttags, $hidden, $keyword, $slug, $category, $limit, $sortedby, $otherthan)
    {
        $qb = $this->createQueryBuilder("b");

        if ($selecttags !== "all") {
            $qb->andWhere(
                $qb->expr()->like("b.tags", ":keyword")
            )->setParameter("keyword", "%" . $selecttags . "%");
        }

        if ($hidden !== "all") {
            $qb->andWhere("b.hidden = :hidden")->setParameter("hidden", $hidden);
        }

        if ($keyword !== "all") {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like("b.name", ":keyword"),
                    $qb->expr()->like("b.content", ":keyword"),
                    $qb->expr()->like("b.tags", ":keyword")
                )
            )->setParameter("keyword", "%" . $keyword . "%");
        }

        if ($slug !== "all") {
            $qb->andWhere("b.slug = :slug")->setParameter("slug", $slug);
        }

        if ($category !== "all") {
            $qb->leftJoin("b.category", "category")
                ->andWhere("category.slug = :category")
                ->setParameter("category", $category);
        }

        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }

        if ($otherthan !== "all") {
            $qb->andWhere("b.id != :otherthan")->setParameter("otherthan", $otherthan);
        }

        switch ($sortedby) {
            case 'created_asc':
                $qb->orderBy("b.createdAt", 'ASC');
                break;
            case 'created_desc':
                $qb->orderBy("b.createdAt", 'DESC');
                break;
            case 'updated_asc':
                $qb->orderBy("b.updatedAt", 'ASC');
                break;
            case 'updated_desc':
                $qb->orderBy("b.updatedAt", 'DESC');
                break;
            case 'title_asc':
                $qb->orderBy("b.name", 'ASC');
                break;
            case 'title_desc':
                $qb->orderBy("b.name", 'DESC');
                break;
            default:
                // Default sorting by created date in descending order
                $qb->orderBy("b.createdAt", 'DESC');
                break;
        }

        return $qb;
    }

    // New method to get blog posts by user ID
    public function getBlogPostsByUserId($user)
    {
        return $this->createQueryBuilder('b')
            ->where('b.user = :user')
            ->setParameter('user', $user);
    }
}
