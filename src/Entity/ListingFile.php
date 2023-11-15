<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;


use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingFileRepository")
 * @ORM\Table(name="universe_listing_file")
 * @Vich\Uploadable
 */
class ListingFile
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\ManyToOne(targetEntity="Listing", inversedBy="files")
     */
    private $listing;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="listing_file", fileNameProperty="fileName", size="fileSize", mimeType="fileMimeType", originalName="fileOriginalName")
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/zip"},
     *     mimeTypesMessage = "The file should be a document or archive",
     * )
     * @var File
     */
    private $listingfile;
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
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

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
     * @return File
     */
    public function getListingfile(): File
    {
        return $this->listingfile;
    }

    /**
     * @param File $listingfile
     */
    public function setListingfile(File $listingfile): void
    {
        $this->listingfile = $listingfile;
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

    public function getId()
    {
        return $this->id;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form), ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true', this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $file
     */
    public function setFile(File $file = null)
    {
//        $this->file = $file;
        $this->listingfile = $file;

        if (null !== $file) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getFilePath()
    {
        return 'uploads/listings/' . $this->fileName;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileSize()
    {
        return $this->fileSize;
    }

    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getFileMimeType()
    {
        return $this->fileMimeType;
    }

    public function setFileMimeType($fileMimeType)
    {
        $this->fileMimeType = $fileMimeType;

        return $this;
    }

    public function getFileOriginalName()
    {
        return $this->fileOriginalName;
    }

    public function setFileOriginalName($fileOriginalName)
    {
        $this->fileOriginalName = $fileOriginalName;

        return $this;
    }

    public function getFileDimensions()
    {
        return $this->fileDimensions;
    }

    public function setFileDimensions($fileDimensions)
    {
        $this->fileDimensions = $fileDimensions;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
