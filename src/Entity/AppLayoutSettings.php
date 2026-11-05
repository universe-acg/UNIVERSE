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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'universe_app_layout_setting')]
#[ORM\Entity(repositoryClass: \App\Repository\AppLayoutSettingsRepository::class)]
#[Vich\Uploadable]
class AppLayoutSettings
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @var File
     */
    #[Assert\File(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'], mimeTypesMessage: 'The file should be an image')]
    #[Vich\UploadableField(mapping: 'app_layout', fileNameProperty: 'logoName', size: 'logoSize', mimeType: 'logoMimeType', originalName: 'logoOriginalName', dimensions: 'logoDimensions')]
    private $logoFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logoName;

    /**
     *
     * @var integer
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $logoSize;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logoMimeType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logoOriginalName;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private $logoDimensions;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @var File
     */
    #[Assert\File(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png', 'image/x-icon'], mimeTypesMessage: 'The file should be an image')]
    #[Vich\UploadableField(mapping: 'app_layout', fileNameProperty: 'faviconName', size: 'faviconSize', mimeType: 'faviconMimeType', originalName: 'faviconOriginalName', dimensions: 'faviconDimensions')]
    private $faviconFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $faviconName;

    /**
     *
     * @var integer
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $faviconSize;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $faviconMimeType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $faviconOriginalName;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private $faviconDimensions;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @var File
     */
    #[Assert\File(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png', 'image/x-icon'], mimeTypesMessage: 'The file should be an image')]
    #[Vich\UploadableField(mapping: 'app_layout', fileNameProperty: 'ogImageName', size: 'ogImageSize', mimeType: 'ogImageMimeType', originalName: 'ogImageOriginalName', dimensions: 'ogImageDimensions')]
    private $ogImageFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ogImageName;

    /**
     *
     * @var integer
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $ogImageSize;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ogImageMimeType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ogImageOriginalName;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private $ogImageDimensions;

    /**
     * @var DateTime $updatedAt
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    public function __construct()
    {

    }

    public function getId()
    {
        return $this->id;
    }

    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $imageFile
     */
    public function setLogoFile(File $logoFile = null)
    {
        $this->logoFile = $logoFile;

        if (null !== $logoFile) {
// It is required that at least one field changes if you are using doctrine
// otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getLogoPath()
    {
        return 'uploads/layout/' . $this->logoName;
    }

    public function getFaviconFile()
    {
        return $this->faviconFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $imageFile
     */
    public function setFaviconFile(File $faviconFile = null)
    {
        $this->faviconFile = $faviconFile;

        if (null !== $faviconFile) {
// It is required that at least one field changes if you are using doctrine
// otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getFaviconPath()
    {
        return 'uploads/layout/' . $this->faviconName;
    }

    public function getOgImageFile()
    {
        return $this->ogImageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $imageFile
     */
    public function setOgImageFile(File $ogImageFile = null)
    {
        $this->ogImageFile = $ogImageFile;

        if (null !== $ogImageFile) {
// It is required that at least one field changes if you are using doctrine
// otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getOgImagePath()
    {
        return 'uploads/layout/' . $this->ogImageName;
    }

    public function getLogoName()
    {
        return $this->logoName;
    }

    public function setLogoName($logoName)
    {
        $this->logoName = $logoName;

        return $this;
    }

    public function getLogoSize()
    {
        return $this->logoSize;
    }

    public function setLogoSize($logoSize)
    {
        $this->logoSize = $logoSize;

        return $this;
    }

    public function getLogoMimeType()
    {
        return $this->logoMimeType;
    }

    public function setLogoMimeType($logoMimeType)
    {
        $this->logoMimeType = $logoMimeType;

        return $this;
    }

    public function getLogoOriginalName()
    {
        return $this->logoOriginalName;
    }

    public function setLogoOriginalName($logoOriginalName)
    {
        $this->logoOriginalName = $logoOriginalName;

        return $this;
    }

    public function getLogoDimensions()
    {
        return $this->logoDimensions;
    }

    public function setLogoDimensions($logoDimensions)
    {
        $this->logoDimensions = $logoDimensions;

        return $this;
    }

    public function getFaviconName()
    {
        return $this->faviconName;
    }

    public function setFaviconName($faviconName)
    {
        $this->faviconName = $faviconName;

        return $this;
    }

    public function getFaviconSize()
    {
        return $this->faviconSize;
    }

    public function setFaviconSize($faviconSize)
    {
        $this->faviconSize = $faviconSize;

        return $this;
    }

    public function getFaviconMimeType()
    {
        return $this->faviconMimeType;
    }

    public function setFaviconMimeType($faviconMimeType)
    {
        $this->faviconMimeType = $faviconMimeType;

        return $this;
    }

    public function getFaviconOriginalName()
    {
        return $this->faviconOriginalName;
    }

    public function setFaviconOriginalName($faviconOriginalName)
    {
        $this->faviconOriginalName = $faviconOriginalName;

        return $this;
    }

    public function getFaviconDimensions()
    {
        return $this->faviconDimensions;
    }

    public function setFaviconDimensions($faviconDimensions)
    {
        $this->faviconDimensions = $faviconDimensions;

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

    public function getOgImageName()
    {
        return $this->ogImageName;
    }

    public function setOgImageName($ogImageName)
    {
        $this->ogImageName = $ogImageName;

        return $this;
    }

    public function getOgImageSize()
    {
        return $this->ogImageSize;
    }

    public function setOgImageSize($ogImageSize)
    {
        $this->ogImageSize = $ogImageSize;

        return $this;
    }

    public function getOgImageMimeType()
    {
        return $this->ogImageMimeType;
    }

    public function setOgImageMimeType($ogImageMimeType)
    {
        $this->ogImageMimeType = $ogImageMimeType;

        return $this;
    }

    public function getOgImageOriginalName()
    {
        return $this->ogImageOriginalName;
    }

    public function setOgImageOriginalName($ogImageOriginalName)
    {
        $this->ogImageOriginalName = $ogImageOriginalName;

        return $this;
    }

    public function getOgImageDimensions()
    {
        return $this->ogImageDimensions;
    }

    public function setOgImageDimensions($ogImageDimensions)
    {
        $this->ogImageDimensions = $ogImageDimensions;

        return $this;
    }

}
