<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\CategoryTranslation;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    class CategoryTranslationRepository extends ServiceEntityRepository
    {
        
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, CategoryTranslation::class);
        }
        
        // Create a custom method to fetch all category translations
        public function findAllCategoryTranslations()
        {
            return $this->createQueryBuilder('ct')
                ->getQuery()
                ->getResult();
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
    }
