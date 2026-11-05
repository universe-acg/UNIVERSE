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

#[ORM\Table(name: 'universe_user_doc1')]
#[ORM\Entity(repositoryClass: \App\Repository\DocDocument1Repository::class)]
#[Assert\Callback(['App\Validation\Validator', 'validate'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
#[Vich\Uploadable]
class DocDocument1
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OneToOne(targetEntity: \App\Entity\User::class)]
    private $user;

    #[Assert\File(maxSize: '5M', mimeTypes: ['application/pdf', 'application/msword'], mimeTypesMessage: 'The file should be a PDF or MS WORD document')]
    #[Vich\UploadableField(mapping: 'doc_file1', fileNameProperty: 'fileName', size: 'fileSize', mimeType: 'fileMimeType', originalName: 'fileOriginalName')]
    private $docfile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $fileSize;

    /**
     * @var DateTime $createdAt
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $fileMimeType;

    /**
     * @var DateTime $updatedAt
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $fileOriginalName;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getDocfile()
    {
        return $this->docfile;
    }

    /**
     * @param mixed $docfile
     */
    public function setDocfile($docfile): void
    {
        $this->docfile = $docfile;
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

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string|null $fileName
     */
    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @param int|null $fileSize
     */
    public function setFileSize(?int $fileSize): void
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
}
