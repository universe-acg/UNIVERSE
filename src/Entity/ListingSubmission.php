<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
    
    use DateTime;
    use Doctrine\ORM\Mapping as ORM;
    use Exception;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Symfony\Component\HttpFoundation\File\File;
    use Symfony\Component\Validator\Constraints as Assert;
    use Vich\UploaderBundle\Mapping\Annotation as Vich;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\ListingSubmisionRepository")
     * @ORM\Table(name="universe_listing_submission")
     * @Vich\Uploadable
     */
    class ListingSubmission
    {
        
        
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;
        /**
         * @ORM\Column(type="text")
         */
        private $content;
        /**
         * @ORM\ManyToOne(targetEntity="Listing", fetch = "EAGER")
         * @ORM\JoinColumn(name="listing_id", referencedColumnName="id")
         */
        private $listing;
        
        /**
         * @ORM\ManyToOne(targetEntity="ListingDatePosition")
         * @ORM\JoinColumn(name="position_id", referencedColumnName="id")
         */
        private $position;
        /**
         * @ORM\ManyToOne(targetEntity="User")
         * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
         */
        private $user;
        
        /**
         * NOTE: This is not a mapped field of entity metadata, just a simple property.
         *
         * @Vich\UploadableField(mapping="listingsubmission_file", fileNameProperty="fileName", size="fileSize", mimeType="fileMimeType", originalName="fileOriginalName", dimensions="fileDimensions")
         * @Assert\File(
         *     maxSize = "32M",
         *     mimeTypesMessage = "The file should be an file"
         *     )
         * @var File
         */
        private $fileFile;
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $fileName;
        /**
         * @ORM\Column(type="integer", nullable=true)
         *
         * @var integer
         */
        private $fileSize;
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $fileMimeType;
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $fileOriginalName;
        /**
         * @ORM\Column(type="simple_array", nullable=true)
         */
        private $fileDimensions;
        /**
         * @var DateTime $createdAt
         *
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(type="datetime")
         */
        private $createdAt;
        
        /**
         * @var DateTime $deletedAt
         * @ORM\Column(type="datetime")
         */
        private $deletedAt;
        /**
         * @var DateTime $updatedAt
         *
         * @Gedmo\Timestampable(on="update")
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $updatedAt;
        
        public function __construct()
        {
        
        }
        
        /**
         * @return DateTime
         */
        public function getDeletedAt()
        {
            return $this->deletedAt;
        }
        
        /**
         * @param DateTime $deletedAt
         */
        public function setDeletedAt(DateTime $deletedAt): void
        {
            $this->deletedAt = $deletedAt;
        }
        
        /**
         * @return File
         */
        public function getFileFile()
        {
            return $this->fileFile;
        }
        
        /**
         * @param File $fileFile
         */
        public function setFileFile(File $fileFile): void
        {
            $this->fileFile = $fileFile;
        }
        
        /**
         * @return mixed
         */
        public function getFileName()
        {
            return $this->fileName;
        }
        
        /**
         * @param mixed $fileName
         */
        public function setFileName($fileName): void
        {
            $this->fileName = $fileName;
        }
        
        /**
         * @return int
         */
        public function getFileSize(): int
        {
            return $this->fileSize;
        }
        
        /**
         * @param int $fileSize
         */
        public function setFileSize(int $fileSize): void
        {
            $this->fileSize = $fileSize;
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
        public function getFileMimeType()
        {
            return $this->fileMimeType;
        }
        
        /**
         * @param mixed $fileMimeType
         */
        public function setFileMimeType($fileMimeType): void
        {
            $this->fileMimeType = $fileMimeType;
        }
        
        /**
         * @return mixed
         */
        public function getFileOriginalName()
        {
            return $this->fileOriginalName;
        }
        
        /**
         * @param mixed $fileOriginalName
         */
        public function setFileOriginalName($fileOriginalName): void
        {
            $this->fileOriginalName = $fileOriginalName;
        }
        
        /**
         * @return mixed
         */
        public function getFileDimensions()
        {
            return $this->fileDimensions;
        }
        
        /**
         * @param mixed $fileDimensions
         */
        public function setFileDimensions($fileDimensions): void
        {
            $this->fileDimensions = $fileDimensions;
        }
        
        /**
         * @return mixed
         */
        public function getPosition()
        {
            return $this->position;
        }
        
        /**
         * @param mixed $position
         */
        public function setPosition($position): void
        {
            $this->position = $position;
        }
        
        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }
        
        /**
         * @param mixed $id
         */
        public function setId($id): void
        {
            $this->id = $id;
        }
        
        /**
         * @throws Exception
         */
        public function generateReference($length): string
        {
            $reference = implode('', [
                bin2hex(random_bytes(2)),
                bin2hex(random_bytes(2)),
                bin2hex(chr((ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
                bin2hex(chr((ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
                bin2hex(random_bytes(2))
            ]);
            return strlen($reference) > $length ? substr($reference, 0, $length) : $reference;
        }
        
        /**
         * @return mixed
         */
        public function getContent()
        {
            return $this->content;
        }
        
        /**
         * @param mixed $content
         */
        public function setContent($content): void
        {
            $this->content = $content;
        }
        
        /**
         * @return mixed
         */
        public function getListing()
        {
            return $this->listing;
        }
        
        /**
         * @param mixed $listing
         */
        public function setListing($listing): void
        {
            $this->listing = $listing;
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
    }
