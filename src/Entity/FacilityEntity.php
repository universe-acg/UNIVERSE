<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Entity;

    use App\Validator\Constraints\EnglishOnly;
    use App\Validator\Constraints\Profanity;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Symfony\Component\HttpFoundation\File\File;
    use Symfony\Component\Validator\Constraints as Assert;
    use Vich\UploaderBundle\Mapping\Annotation as Vich;

    #[ORM\Table(name: 'universe_facility')]
    #[ORM\Entity(repositoryClass: \App\Repository\FacilityRepository::class)]
    #[Assert\Callback(['App\Validation\Validator', 'validate'])]
    #[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
    #[Vich\Uploadable]
    class FacilityEntity
    {
        #[ORM\Column(type: 'boolean')]
        private $hidden = false;

        #[ORM\OneToMany(targetEntity: \App\Entity\MonthlySchedule::class, mappedBy: 'facility', cascade: ['persist', 'remove'], orphanRemoval: true, fetch: 'LAZY')]
        private $monthlySchedule;

        #[Gedmo\Timestampable(on: 'create')]
        #[ORM\Column(type: 'datetime')]
        private $createdAt;

        /**
         * NOTE: This is not a mapped field of entity metadata, just a simple property.
         *
         * @var File
         */
        #[Assert\File(maxSize: '32M', mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'], mimeTypesMessage: 'The file should be an image')]
        #[Vich\UploadableField(mapping: 'facility_main_image', fileNameProperty: 'mainImage', size: 'imageSize', mimeType: 'imageMimeType', originalName: 'imageOriginalName', dimensions: 'imageDimensions')]
        private $mainImageFile;

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $mainImage;

        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private $id;

        #[ORM\Column(type: 'integer')]
        private $duration;

        #[ORM\Column(type: 'string', length: 255)]
        private $name;

        #[ORM\Column(type: 'text', nullable: true)]
        private $description;

        #[ORM\Column(type: 'integer')]
        private $minPlayers;

        #[ORM\Column(type: 'integer')]
        private $maxPlayers;
        /**
         * @EnglishOnly
         * @Profanity
         */
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $reference;
        #[ORM\Column(type: 'integer', nullable: true)]
        private $views;
        /**
         * @EnglishOnly
         * @Profanity
         */
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $externallink;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $phonenumber;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        #[Assert\Email]
        private $email;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $tags;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $imageName;
        #[ORM\Column(type: 'integer', nullable: true)]
        private $imageSize;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $imageMimeType;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $imageOriginalName;
        #[ORM\Column(type: 'simple_array', nullable: true)]
        private $imageDimensions;
        #[Gedmo\Timestampable(on: 'create')]
        #[ORM\Column(type: 'datetime')]
        private $updatedAt;
        #[ORM\Column(type: 'datetime', nullable: true)]
        private $deleted_at;
        #[ORM\Column(type: 'boolean')]
        private $published;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        #[Gedmo\Slug(fields: ['name'], updatable: true)]
        private $slug;
        #[ORM\OneToMany(targetEntity: \App\Entity\FacilityEntityImage::class, mappedBy: 'facility', cascade: ['persist'])]
        private $secondaryImages;


        public function __construct()
        {
            $this->secondaryImages = new ArrayCollection();
            $this->monthlySchedule = new ArrayCollection();
        }


        /**
         * Add a monthly schedule to the collection.
         *
         * @param MonthlySchedule $monthlySchedule
         */
        public function addMonthlySchedule(MonthlySchedule $monthlySchedule): void
        {
            // Set the association
            $monthlySchedule->setFacility($this);

            // Add the monthly schedule to the collection
            $this->monthlySchedule->add($monthlySchedule);
        }

        /**
         * Remove a monthly schedule from the collection.
         *
         * @param MonthlySchedule $monthlySchedule
         */
        public function removeMonthlySchedule(MonthlySchedule $monthlySchedule): void
        {
            // Remove the monthly schedule from the collection
            $this->monthlySchedule->removeElement($monthlySchedule);
        }

        /**
         * @return mixed
         */
        public function getMonthlySchedule()
        {
            return $this->monthlySchedule;
        }

        /**
         * @param mixed $monthlySchedule
         */
        public function setMonthlySchedule($monthlySchedule): void
        {
            $this->monthlySchedule = $monthlySchedule;
        }

        /**
         * @return mixed
         */
        public function getCreatedAt()
        {
            return $this->createdAt;
        }

        /**
         * @param mixed $createdAt
         */
        public function setCreatedAt($createdAt): void
        {
            $this->createdAt = $createdAt;
        }

        /**
         * @return mixed
         */
        public function getUpdatedAt()
        {
            return $this->updatedAt;
        }

        /**
         * @param mixed $updatedAt
         */
        public function setUpdatedAt($updatedAt): void
        {
            $this->updatedAt = $updatedAt;
        }

        /**
         * @return bool
         */
        public function isHidden(): bool
        {
            return $this->hidden == true;
        }

        /**
         * @param bool $hidden
         */
        public function setHidden(bool $hidden): void
        {
            $this->hidden = $hidden;
        }

        /**
         * @return mixed
         */
        public function getMainImageFile()
        {
            return $this->mainImageFile;
        }

        /**
         * @param File $mainImageFile
         */
        public function setMainImageFile(File $mainImageFile): void
        {
            $this->mainImageFile = $mainImageFile;
        }

        /**
         * @return mixed
         */
        public function getMainImage()
        {
            return $this->mainImage;
        }

        /**
         * @param mixed $mainImage
         */
        public function setMainImage($mainImage): void
        {
            $this->mainImage = $mainImage;
        }

        public function getImagePlaceholder($size = "default")
        {
//        if ($size == "small") {
//            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAV4AAAFeCAMAAAD69YcoAAAAyVBMVEUAAADd3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d0jcjdZAAAAQnRSTlMAAQIEBQYLDA8SExQWFxkaHyAhJCUmKi06O0lRVVZfYmNkb3V3g46PkZKbo6ivtLW5vL7FyMrT19na3Ojr7fHz9/luoWfVAAACeUlEQVR42u3cBXKDQBiAUaDukkrqSb1N3d3uf6jeoJbuBtj3nWD3zTDAvwxZJkmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJElSjD5qWxsvXrx48eLFixcvXrx48eLFixcvXrwl571oV7cK8JZiTTXaCl68eG0FrzXhxYsXL168ePHixYsXr62kwXuz/2WbeLvayjdd4sWLFy9evHjx4sWLFy9evHjx4sX7B96ir7zl1efdL/H30Qt48eLFixcvXrx48eLFixcvXrx48eKtF+9Yo7wNOK1wWoEXL168ePHixYsXL168ePHixYsXL94f8/7vOL3RFYjTCrx48eLFixcvXrx48eLFixcvXrx48XbBm/wHqGF5jdPx4sWLFy9evHjx4sWLFy9evHjx4sVrnI4XL168ePHixYsXL168ePHixYsXL168eI3T8eLFixcvXrx48eLFixcvXrx48eLFW07e3/1gYAJvyHF6By9evHjx4sWLFy9evHjx4sWLFy9evPXmHWv8Z/3mvdUNL168ePHixYsXbyV5L/CG5D3CG5L3AG9I3hbekLxNvCF5R/AG5H3N8Abk3YmzprePNJuOw/uQpu59pCuqkyZvMxLvepK6d3kk3pkkeedi3W7zpwR1d+M9zqykp3tbxOMtHlPTfRmO+TS+lJju+2Tct529pHSfJ+LqZsV1QrpXQ9Ff1gdOk9HdyrP45dtp4J6P92jaNHVWf9zj2R6O8+YPa217sjLY43lpMdVc3WjXrtba8uJokUmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmS1Js+AbSytGbYpVXAAAAAAElFTkSuQmCC";
//        }
//        return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABgAAAAYACAMAAACHMHNZAAABVlBMVEUAAADd3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d18zGJfAAAAcXRSTlMAAQIDBAUHCAkLDA0PEBESExQWFxocHiAjJCUmKywtLzA0NTY3ODk6Pj9AQUJHSU1OT1BYWV9jZGZoaWtsbW90dXd7fH5/goOFjpSXmJqeoKOlpqq1t7nBw8XKzs/T1dfZ3N7g4uTm6+3v8fP19/n7/XM0K4AAABCKSURBVHja7d1bbxR1HMfhBSe1EKkYUEAjkaioCWjwUmO4IF544ZXR97GvZN+FhlTlwqChRoyHiASCLaek0FLLSqEFSkuB3e62XTmESqHF0m6Znfk9zz1Rvo799N/OzBYKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAECrWxV9gKJrAFJTMkGqVpsAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQCg2RITLE/JBARWNIETAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAA9KTACkpZjyP7/kBACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAEBzJSZIV9EEpKhkAicAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAAmikxQbpKJgCcAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABABAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQAQAAAEAAABAEAAABAAAPIiMUG6iiZYlpL9U90PJwAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAADmSExAZCUT4AQAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgBA8yQmILJi8L9/ySXgBACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAEAzJSYgspIJcAIAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEA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";
            return '/uploads/imgs/default.jpg';
        }

        public function getImagePath()
        {
            return 'uploads/facilities/' . $this->mainImage;
        }

        public function getReservationsCount()
        {
            return 0;
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
        public function getDuration()
        {
            return $this->duration;
        }

        /**
         * @param mixed $duration
         */
        public function setDuration($duration): void
        {
            $this->duration = $duration;
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name): void
        {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getDescription()
        {
            return $this->description;
        }

        /**
         * @param mixed $description
         */
        public function setDescription($description): void
        {
            $this->description = $description;
        }

        /**
         * @return mixed
         */
        public function getMinPlayers()
        {
            return $this->minPlayers;
        }

        /**
         * @param mixed $minPlayers
         */
        public function setMinPlayers($minPlayers): void
        {
            $this->minPlayers = $minPlayers;
        }

        /**
         * @return mixed
         */
        public function getMaxPlayers()
        {
            return $this->maxPlayers;
        }

        /**
         * @param mixed $maxPlayers
         */
        public function setMaxPlayers($maxPlayers): void
        {
            $this->maxPlayers = $maxPlayers;
        }

        /**
         * @return mixed
         */
        public function getReference()
        {
            return $this->reference;
        }

        /**
         * @param mixed $reference
         */
        public function setReference($reference): void
        {
            $this->reference = $reference;
        }

        /**
         * @return mixed
         */
        public function getViews()
        {
            return $this->views;
        }

        /**
         * @param mixed $views
         */
        public function setViews($views): void
        {
            $this->views = $views;
        }

        /**
         * @return mixed
         */
        public function getExternallink()
        {
            return $this->externallink;
        }

        /**
         * @param mixed $externallink
         */
        public function setExternallink($externallink): void
        {
            $this->externallink = $externallink;
        }

        /**
         * @return mixed
         */
        public function getPhonenumber()
        {
            return $this->phonenumber;
        }

        /**
         * @param mixed $phonenumber
         */
        public function setPhonenumber($phonenumber): void
        {
            $this->phonenumber = $phonenumber;
        }

        /**
         * @return mixed
         */
        public function getEmail()
        {
            return $this->email;
        }

        /**
         * @param mixed $email
         */
        public function setEmail($email): void
        {
            $this->email = $email;
        }

        /**
         * @return mixed
         */
        public function getTags()
        {
            return $this->tags;
        }

        /**
         * @param mixed $tags
         */
        public function setTags($tags): void
        {
            $this->tags = $tags;
        }

        /**
         * @return mixed
         */
        public function getImageName()
        {
            return $this->imageName;
        }

        /**
         * @param mixed $imageName
         */
        public function setImageName($imageName): void
        {
            $this->imageName = $imageName;
        }

        /**
         * @return mixed
         */
        public function getImageSize()
        {
            return $this->imageSize;
        }

        /**
         * @param mixed $imageSize
         */
        public function setImageSize($imageSize): void
        {
            $this->imageSize = $imageSize;
        }

        /**
         * @return mixed
         */
        public function getImageMimeType()
        {
            return $this->imageMimeType;
        }

        /**
         * @param mixed $imageMimeType
         */
        public function setImageMimeType($imageMimeType): void
        {
            $this->imageMimeType = $imageMimeType;
        }

        /**
         * @return mixed
         */
        public function getImageOriginalName()
        {
            return $this->imageOriginalName;
        }

        /**
         * @param mixed $imageOriginalName
         */
        public function setImageOriginalName($imageOriginalName): void
        {
            $this->imageOriginalName = $imageOriginalName;
        }

        /**
         * @return mixed
         */
        public function getImageDimensions()
        {
            return $this->imageDimensions;
        }

        /**
         * @param mixed $imageDimensions
         */
        public function setImageDimensions($imageDimensions): void
        {
            $this->imageDimensions = $imageDimensions;
        }

        /**
         * @return mixed
         */
        public function getDeletedAt()
        {
            return $this->deleted_at;
        }

        /**
         * @param mixed $deleted_at
         */
        public function setDeletedAt($deleted_at): void
        {
            $this->deleted_at = $deleted_at;
        }

        /**
         * @return mixed
         */
        public function getPublished()
        {
            return $this->published;
        }

        /**
         * @param mixed $published
         */
        public function setPublished($published): void
        {
            $this->published = $published;
        }

        /**
         * @return mixed
         */
        public function getSlug()
        {
            return $this->slug;
        }

        /**
         * @param mixed $slug
         */
        public function setSlug($slug): void
        {
            $this->slug = $slug;
        }

        /**
         * @return ArrayCollection
         */
        public function getSecondaryImages()
        {
            return $this->secondaryImages;
        }

        public function addSecondaryImage(FacilityEntityImage $image)
        {
            $image->setFacility($this);
            $this->secondaryImages->add($image);
        }

        public function removeSecondaryImage(FacilityEntityImage $image)
        {
            $this->secondaryImages->removeElement($image);
        }
    }
