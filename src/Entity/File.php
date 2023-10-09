<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
    use DateTime;
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Symfony\Component\Validator\Constraints as Assert;
    use Vich\UploaderBundle\Mapping\Annotation as Vich;
    
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
     * @ORM\Table(name="universe_file")
     * @Vich\Uploadable
     */
    class File
    {
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;
        
        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\User", fetch="EAGER")
         * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
         */
        private $user;
        
        /**
         * NOTE: This is not a mapped field of entity metadata, just a simple property.
         *
         * @Vich\UploadableField(mapping="file_file", fileNameProperty="fileName", size="fileSize", mimeType="fileMimeType", originalName="fileOriginalName")
         * @Assert\File(
         *     maxSize = "5M",
         *     )
         * @var \Symfony\Component\HttpFoundation\File\File
         */
        private $fileFile;
        /**
         * @ORM\Column(type="string", length=255, nullable=false)
         */
        private ?string $fileName;
        /**
         * @ORM\Column(type="integer")
         */
        private ?int $fileSize;
        /**
         * @var DateTime $createdAt
         *
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(type="datetime")
         */
        private $createdAt;
        /**
         * @ORM\Column(type="string", length=255)
         */
        private $fileMimeType;
        /**
         * @var DateTime $updatedAt
         *
         * @Gedmo\Timestampable(on="update")
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $updatedAt;
        /**
         * @ORM\Column(type="string", length=255)
         */
        private $fileOriginalName;
        
        /**
         * @ORM\Column(type="string", length=255)
         */
        private $title;
        
        
        /**
         * @return \Symfony\Component\HttpFoundation\File\File
         */
        public function getFileFile()
        {
            return $this->fileFile;
        }
        
        /**
         * @param \Symfony\Component\HttpFoundation\File\File|null $fileFile
         */
        public function setFileFile(\Symfony\Component\HttpFoundation\File\File $fileFile = null): void
        {
            $this->fileFile = $fileFile;
        }
        
        
        /**
         * @return DateTime
         */
        public function getUpdatedAt(): DateTime
        {
            return $this->updatedAt;
        }
        
        /**
         * @param DateTime $updatedAt
         */
        public function setUpdatedAt(DateTime $updatedAt): void
        {
            $this->updatedAt = $updatedAt;
        }
        
        /**
         * @return mixed
         */
        public function getTitle()
        {
            return $this->title;
        }
        
        /**
         * @param mixed $title
         */
        public function setTitle($title): void
        {
            $this->title = $title;
        }
        
        /**
         * @return DateTime
         */
        public function getCreatedAt(): DateTime
        {
            return $this->createdAt;
        }
        
        /**
         * @param DateTime $createdAt
         */
        public function setCreatedAt(DateTime $createdAt): void
        {
            $this->createdAt = $createdAt;
        }
        
        public function getId(): ?int
        {
            return $this->id;
        }
        
        /**
         * @return mixed
         */
        public function getUser()
        {
            return $this->user;
        }
        
        /**
         * @param mixed $user
         */
        public function setUser($user): void
        {
            $this->user = $user;
        }
        
        
        public function getFileName()
        {
            return $this->fileName ?? "";
        }
        
        public function setFileName(string $fileName = null): self
        {
            $this->fileName = $fileName;
            return $this;
        }
        
        public function getFileSize()
        {
            return $this->fileSize;
        }
        
        public function setFileSize(int $fileSize = null): self
        {
            $this->fileSize = $fileSize;
            
            return $this;
        }
        
        public function getFileMimeType()
        {
            return $this->fileMimeType;
        }
        
        public function setFileMimeType(string $fileMimeType = null): self
        {
            $this->fileMimeType = $fileMimeType;
            
            return $this;
        }
        
        public function getFileOriginalName()
        {
            return $this->fileOriginalName;
        }
        
        public function setFileOriginalName(string $fileOriginalName = null): self
        {
            $this->fileOriginalName = $fileOriginalName;
            
            return $this;
        }
    }
