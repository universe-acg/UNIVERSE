<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\Comment;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\ORM\NonUniqueResultException;
    use Doctrine\Persistence\ManagerRegistry;
    
    /**
     * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
     * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
     * @method Comment[]    findAll()
     * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class CommentRepository extends ServiceEntityRepository
    {
        
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Comment::class);
        }
        
        public function findByExampleField($value)
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.exampleField = :val')
                ->setParameter('val', $value)
                ->orderBy('c.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        }
        
        public function findOneBySomeField($value): ?Comment
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.exampleField = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        /**
         * Find a comment by its ID.
         *
         * @param int $id The ID of the comment.
         * @return Comment|null The comment entity or null if not found.
         * @throws NonUniqueResultException
         */
        public function findById(int $id): ?Comment
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }
        
        /**
         * Find comments by author ID.
         *
         * @param int $authorId The ID of the author.
         * @return Comment[]|null An array of comments by the author or null if not found.
         */
        public function findByAuthor(int $authorId)
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.author = :authorId')
                ->setParameter('authorId', $authorId);
        }
    }
