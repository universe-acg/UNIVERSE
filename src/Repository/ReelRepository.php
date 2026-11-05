<?php
namespace App\Repository;
use App\Entity\Reel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ReelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry,Reel::class); }
    public function getReels($hidden,$keyword,$slug,$limit,$count)
    {
        $qb=$this->getReelssql($hidden,$keyword,$slug,$limit,$count);
        return $count ? (int)$qb->getQuery()->getSingleScalarResult() : $qb->getQuery()->getResult();
    }
    public function getReelssql($hidden,$keyword,$slug,$limit,$count)
    {
        $qb=$this->createQueryBuilder('r')->select($count?'COUNT(r.id)':'r');
        if ($hidden!=='all') $qb->andWhere('r.hidden=:hidden AND r.deletedAt IS NULL')->setParameter('hidden',$hidden);
        if ($keyword!=='all' && trim((string)$keyword)!=='') $qb->andWhere('r.title LIKE :keyword OR r.content LIKE :keyword OR r.tags LIKE :keyword')->setParameter('keyword','%'.$keyword.'%');
        if ($slug!=='all') $qb->andWhere('r.slug=:slug')->setParameter('slug',$slug);
        if ($limit!=='all') $qb->setMaxResults(max(1,(int)$limit));
        if (!$count) $qb->orderBy('r.createdAt','DESC')->addOrderBy('r.id','DESC');
        return $qb;
    }
}
