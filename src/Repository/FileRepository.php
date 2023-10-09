<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Repository;
    
    use App\Entity\File;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
    /**
     */
    class FileRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, File::class);
        }
        
        // Get files of a user by user ID
        public function getFilesByUserId(int $userId)
        {
            return $this->createQueryBuilder('f')
                ->where('f.user = :userId')
                ->setParameter('userId', $userId)
                ->getQuery()
                ->getResult();
        }
        
        // Update a file by ID
        public function updateFileById(int $fileId, array $data)
        {
            $file = $this->find($fileId);
            
            if (!$file) {
                return null; // File not found
            }
            
            // Update file properties based on the $data array
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'fileName':
                        $file->setFileName($value);
                        break;
                    case 'fileSize':
                        $file->setFileSize($value);
                        break;
                    // Add more cases for other properties as needed
                }
            }
            
            $entityManager = $this->getEntityManager();
            $entityManager->persist($file);
            $entityManager->flush();
            
            return $file; // Return the updated file entity
        }
        
        // Delete a file by ID
        public function deleteFileById(int $fileId)
        {
            $file = $this->find($fileId);
            
            if (!$file) {
                return false; // File not found
            }
            
            $entityManager = $this->getEntityManager();
            $entityManager->remove($file);
            $entityManager->flush();
            
            return true; // File successfully deleted
        }
        
        // Get all files
        public function getAllFiles()
        {
            return $this->findAll();
        }
        
        // Get a file by its ID
        public function getFileById(int $fileId)
        {
            return $this->find($fileId);
        }
    }
