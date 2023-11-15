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
 * @ORM\Entity(repositoryClass="App\Repository\DocDocumentRepository")
 * @ORM\Table(name="universe_user_doc")
 * @Assert\Callback({"App\Validation\Validator", "validate"})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Vich\Uploadable
 */
class DocDocumentDTO
{
    private $deletedocfile2;
    private $deletedocfile1;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="doc")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    /**
     * @Vich\UploadableField(mapping="doc_file1", fileNameProperty="fileName", size="fileSize", mimeType="fileMimeType", originalName="fileOriginalName")
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"application/pdf", "application/msword"},
     *     mimeTypesMessage = "The file should be a PDF or MS WORD document",
     * )
     */
    private $docfile1;
    /**
     * @Vich\UploadableField(mapping="doc_file2", fileNameProperty="fileName1", size="fileSize1", mimeType="fileMimeType1", originalName="fileOriginalName1")
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"application/pdf", "application/msword"},
     *     mimeTypesMessage = "The file should be a PDF or MS WORD document",
     * )
     */
    private $docfile2;
    /**
     * @var DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @var DateTime $updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $fileName = null;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $fileSize;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileMimeType;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileOriginalName;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $fileName1 = null;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $fileSize1;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileMimeType1;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileOriginalName1;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getDeletedocfile2()
    {
        return $this->deletedocfile2;
    }

    /**
     * @param mixed $deletedocfile2
     */
    public function setDeletedocfile2($deletedocfile2): void
    {
        $this->deletedocfile2 = $deletedocfile2;
    }

    /**
     * @return mixed
     */
    public function getDeletedocfile1()
    {
        return $this->deletedocfile1;
    }

    /**
     * @param mixed $deletedocfile1
     */
    public function setDeletedocfile1($deletedocfile1): void
    {
        $this->deletedocfile1 = $deletedocfile1;
    }

    /**
     * @return string|null
     */
    public function getFileName1(): ?string
    {
        return $this->fileName1;
    }

    /**
     * @param string|null $fileName1
     */
    public function setFileName1(?string $fileName1): void
    {
        $this->fileName1 = $fileName1;
    }

    /**
     * @return int|null
     */
    public function getFileSize1(): ?int
    {
        return $this->fileSize1;
    }

    /**
     * @param int|null $fileSize1
     */
    public function setFileSize1(?int $fileSize1): void
    {
        $this->fileSize1 = $fileSize1;
    }

    /**
     * @return mixed
     */
    public function getFileMimeType1()
    {
        return $this->fileMimeType1;
    }

    /**
     * @param mixed $fileMimeType1
     */
    public function setFileMimeType1($fileMimeType1): void
    {
        $this->fileMimeType1 = $fileMimeType1;
    }

    /**
     * @return mixed
     */
    public function getFileOriginalName1()
    {
        return $this->fileOriginalName1;
    }

    /**
     * @param mixed $fileOriginalName1
     */
    public function setFileOriginalName1($fileOriginalName1): void
    {
        $this->fileOriginalName1 = $fileOriginalName1;
    }

    /**
     * @return mixed
     */
    public function getDocfile1()
    {
        return $this->docfile1;
    }

    /**
     * @param mixed $docfile1
     */
    public function setDocfile1($docfile1): void
    {
        $this->docfile1 = $docfile1;
    }

    /**
     * @return mixed
     */
    public function getDocfile2()
    {
        return $this->docfile2;
    }

    /**
     * @param mixed $docfile2
     */
    public function setDocfile2($docfile2): void
    {
        $this->docfile2 = $docfile2;
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
