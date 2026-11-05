<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'universe_venue_images')]
#[ORM\Entity(repositoryClass: \App\Repository\VenueImageRepository::class)]
#[Vich\Uploadable]
class VenueImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Venue::class, inversedBy: 'secondaryImages', cascade: ['persist'])]
    private $venue;
    #[ORM\Column(name: 'position', type: 'integer', nullable: true)]
    private $position;
    /**
     * @var File
     */
    #[Assert\File(maxSize: '8M', mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'], mimeTypesMessage: 'The file should be an image')]
    #[Vich\UploadableField(mapping: 'venue_image', fileNameProperty: 'imageName', size: 'imageSize', mimeType: 'imageMimeType', originalName: 'imageOriginalName')]
    private $imageFile;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imageName;

    /**
     *
     * @var ?integer
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $imageSize;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imageMimeType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imageOriginalName;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private $imageDimensions;

    /**
     * @var DateTime $updatedAt
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

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

    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $imageFile
     */
    public function setImageFile(File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->imageSize = $imageFile->getSize(); // Set the size of the uploaded file
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize()
    {
        return $this->imageSize;
    }

    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageMimeType()
    {
        return $this->imageMimeType;
    }

    public function setImageMimeType($imageMimeType)
    {
        $this->imageMimeType = $imageMimeType;

        return $this;
    }

    public function getImageOriginalName()
    {
        return $this->imageOriginalName;
    }

    public function setImageOriginalName($imageOriginalName)
    {
        $this->imageOriginalName = $imageOriginalName;

        return $this;
    }

    public function getImageDimensions()
    {
        return $this->imageDimensions;
    }

    public function setImageDimensions($imageDimensions)
    {
        $this->imageDimensions = $imageDimensions;

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

    public function getImagePath()
    {
        return 'uploads/venues/' . $this->imageName;
    }

    /**
     * @return mixed
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * @param mixed $venue
     */
    public function setVenue($venue): void
    {
        $this->venue = $venue;
    }
}
