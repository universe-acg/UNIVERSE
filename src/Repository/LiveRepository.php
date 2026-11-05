<?php
namespace App\Repository;
use App\Entity\Live;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class LiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry,Live::class); }
    public function getLives($hidden,$keyword,$slug,$limit,$count)
    {
        $qb=$this->getLivessql($hidden,$keyword,$slug,$limit,$count);
        return $count ? (int)$qb->getQuery()->getSingleScalarResult() : $qb->getQuery()->getResult();
    }
    public function getLivessql($hidden,$keyword,$slug,$limit,$count)
    {
        $qb=$this->createQueryBuilder('l')->select($count?'COUNT(l.id)':'l');
        if ($hidden!=='all') $qb->andWhere('l.hidden=:hidden AND l.deletedAt IS NULL')->setParameter('hidden',$hidden);
        if ($keyword!=='all' && trim((string)$keyword)!=='') $qb->andWhere('l.title LIKE :keyword OR l.description LIKE :keyword')->setParameter('keyword','%'.$keyword.'%');
        if ($slug!=='all') $qb->andWhere('l.slug=:slug')->setParameter('slug',$slug);
        if ($limit!=='all') $qb->setMaxResults(max(1,(int)$limit));
        if (!$count) $qb->orderBy('l.createdAt','DESC')->addOrderBy('l.id','DESC');
        return $qb;
    }
}
