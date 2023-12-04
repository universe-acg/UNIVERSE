<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Symfony\Component\HttpFoundation\File\File;
    use Symfony\Component\Validator\Constraints as Assert;
    use Vich\UploaderBundle\Mapping\Annotation as Vich;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\VenueRepository")
     * @ORM\Table(name="universe_venue")
     * @Assert\Callback({"App\Validation\Validator", "validate"})
     * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
     * @Vich\Uploadable
     */
    class Venue
    {
        
        /**
         * @ORM\OneToMany(targetEntity="Event", mappedBy="venue", fetch="LAZY")
         */
        private $events;
        
        private $submitdefaultcv;
        /**
         * NOTE: This is not a mapped field of entity metadata, just a simple property.
         *
         * @Vich\UploadableField(mapping="venue_main_image", fileNameProperty="mainImage", size="imageSize", mimeType="imageMimeType", originalName="imageOriginalName", dimensions="imageDimensions")
         * @Assert\File(
         *     maxSize = "32M",
         *     mimeTypes = {"image/jpeg", "image/jpg", "image/gif", "image/png"},
         *     mimeTypesMessage = "The file should be an image"
         *     )
         * @var File
         */
        private $mainImageFile;
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $mainImage;
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;
        /**
         * @ORM\Column(length=255, unique=true)
         * @Gedmo\Slug(fields={"title"}, updatable=true)
         */
        private $slug;
        
        // Getters and setters...
        /**
         * @ORM\Column(type="string", length=500, nullable=true)
         */
        private $availability;
        /**
         * @ORM\OneToMany(targetEntity="VenueImage", mappedBy="venue", cascade={"persist"})
         */
        private $secondaryImages;
        /**
         * @ORM\Column(type="boolean")
         */
        private $hidden;
        /**
         * @ORM\Column(type="string", length=50, nullable=true)
         * @Assert\NotBlank
         */
        private $title;
        /**
         * @ORM\Column(type="text", nullable=true)
         * @Assert\NotBlank
         */
        private $description;
        /**
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $contactEmail;
        /**
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(type="datetime")
         */
        private $createdAt;
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $imageName;
        /**
         * @ORM\Column(type="integer", nullable=true)
         *
         * @var integer
         */
        private $imageSize;
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $imageMimeType;
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $imageOriginalName;
        /**
         * @ORM\Column(type="simple_array", nullable=true)
         */
        private $imageDimensions;
        /**
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(type="datetime")
         */
        private $updatedAt;
        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $deletedAt;
        /**
         * @ORM\ManyToMany(targetEntity="Amenity")
         * @ORM\JoinTable(name="universe_venue_amenities")
         */
        private $amenities;
        
        public function __construct()
        {
            $this->events = new ArrayCollection();
            $this->secondaryImages = new ArrayCollection();
            $this->amenities = new ArrayCollection();
        }
        
        /**
         * @return mixed
         */
        public function getSubmitdefaultcv()
        {
            return $this->submitdefaultcv;
        }
        
        /**
         * @param mixed $submitdefaultcv
         */
        public function setSubmitdefaultcv($submitdefaultcv): void
        {
            $this->submitdefaultcv = $submitdefaultcv;
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
         * @return int
         */
        public function getImageSize()
        {
            return $this->imageSize;
        }
        
        /**
         * @param int $imageSize
         */
        public function setImageSize(int $imageSize): void
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
        
        public function getImagePath()
        {
            return 'uploads/venues/' . $this->mainImage;
        }
        
        /**
         * Get the count of associated events
         */
        public function getEventsCount(): int
        {
            return $this->events->count();
        }
        
        public function getEvents()
        {
            return $this->events;
        }
        
        /**
         * Get the count of associated event dates
         */
        public function getEventDatesCount()
        {
            $eventDatesCount = 0;
            
            foreach ($this->events as $event) {
                $eventDatesCount += $event->getEventDates()->count();
            }
            
            return $eventDatesCount;
        }
        
        public function addEvent($event)
        {
            $this->events->add($event);
            $event->setVenue($this);
        }
        
        public function removeEvent($event)
        {
            $this->events->removeElement($event);
            $event->setVenue(null);
        }
        
        /**
         * @return mixed
         */
        public function getMainImageFile()
        {
            return $this->mainImageFile;
        }
        
        /**
         * @param mixed $mainImageFile
         */
        public function setMainImageFile($mainImageFile): void
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
            return 'http://localhost/newunvierse/uploads/imgs/default.jpg';
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
        public function getAvailability()
        {
            return $this->availability;
        }
        
        /**
         * @param mixed $availability
         */
        public function setAvailability($availability): void
        {
            $this->availability = $availability;
        }
        
        /**
         * @return mixed
         */
        public function getHidden()
        {
            return $this->hidden;
        }
        
        /**
         * @param mixed $hidden
         */
        public function setHidden($hidden): void
        {
            $this->hidden = $hidden;
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
        public function getContactEmail()
        {
            return $this->contactEmail;
        }
        
        /**
         * @param mixed $contactEmail
         */
        public function setContactEmail($contactEmail): void
        {
            $this->contactEmail = $contactEmail;
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
         * @return ArrayCollection|Amenity[]
         */
        public function getAmenities()
        {
            return $this->amenities;
        }
        
        public function addAmenity(Amenity $amenity)
        {
            if (!$this->amenities->contains($amenity)) {
                $this->amenities[] = $amenity;
            }
        }
        
        public function removeAmenity(Amenity $amenity)
        {
            $this->amenities->removeElement($amenity);
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
         * @return mixed
         */
        public function getDeletedAt()
        {
            return $this->deletedAt;
        }
        
        /**
         * @param mixed $deletedAt
         */
        public function setDeletedAt($deletedAt): void
        {
            $this->deletedAt = $deletedAt;
        }
        
        /**
         * @return ArrayCollection
         */
        public function getSecondaryImages()
        {
            return $this->secondaryImages;
        }
        
        public function addSecondaryImage(VenueImage $image)
        {
            $this->secondaryImages->add($image);
            $image->setVenue($this);
        }
        
        public function removeSecondaryImage(VenueImage $image)
        {
            $this->secondaryImages->removeElement($image);
        }
    }