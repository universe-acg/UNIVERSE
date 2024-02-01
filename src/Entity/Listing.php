<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
    use App\Validator\Constraints\EnglishOnly;
    use App\Validator\Constraints\Profanity;
    use DateTime;
    use DateTimeImmutable;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Exception;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Symfony\Component\HttpFoundation\File\File;
    use Symfony\Component\Validator\Constraints as Assert;
    use Vich\UploaderBundle\Mapping\Annotation as Vich;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\ListingRepository")
     * @ORM\Table(name="universe_listing")
     * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
     * @Vich\Uploadable
     */
    class Listing
    {
        
        /**
         * @ORM\Column(type="string", length=255, nullable=false)
         */
        protected $name;
        /**
         * @ORM\Column(type="text", nullable=true)
         */
        protected $description;
        /**
         * @ORM\Column(type="text", nullable=true)
         */
        protected $shortdescription;
        /**
         * @Gedmo\Slug(fields={"name"}, updatable=false)
         * @ORM\Column(length=255, unique=true)
         */
        protected $slug;
        /**
         * @ORM\OneToMany(targetEntity="ListingDatePosition", mappedBy="listing", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER")
         */
        private $datePositions;
        
        /**
         * @ORM\ManyToOne(targetEntity="Event")
         * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
         */
        private $event;
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;
        /**
         * @ORM\ManyToOne(targetEntity="ListingCategory", inversedBy="listings", fetch = "EAGER")
         */
        private $category;
        /**
         * @ORM\ManyToOne(targetEntity="Organizer", inversedBy="listings", fetch = "EAGER")
         */
        private $organizer;
        /**
         * @ORM\OneToMany(targetEntity="ListingImage", mappedBy="listing", cascade={"persist", "remove"}, orphanRemoval=true, fetch = "EAGER")
         */
        private $images;
        /**
         * @ORM\OneToMany(targetEntity="ListingFile", mappedBy="listing", cascade={"persist", "remove"}, orphanRemoval=true, fetch = "EAGER")
         */
        private $files;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255)
         */
        private $reference;
        /**
         * @var integer
         * @ORM\Column(type="integer")
         */
        private $views;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255, nullable=true)
         * @EnglishOnly
         * @Profanity
         */
        private $externallink;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255, nullable=true)
         * @EnglishOnly
         * @Profanity
         */
        private $phonenumber;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255, nullable=true)
         * @EnglishOnly
         * @Profanity
         */
        private $email;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255, nullable=true)
         * @EnglishOnly
         * @Profanity
         */
        private $tags;
        /**
         * NOTE: This is not a mapped field of entity metadata, just a simple property.
         *
         * @Vich\UploadableField(mapping="listing_image", fileNameProperty="imageName", size="imageSize", mimeType="imageMimeType", originalName="imageOriginalName", dimensions="imageDimensions")
         * @Assert\File(
         *     maxSize = "32M",
         *     mimeTypes = {"image/jpeg", "image/jpg", "image/gif", "image/png"},
         *     mimeTypesMessage = "The file should be an image"
         *     )
         * @var File
         */
        private $imageFile;
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
        private $typeoflisting;
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $imageOriginalName;
        /**
         * @ORM\Column(type="simple_array", nullable=true)
         */
        private $imageDimensions;
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
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $deletedAt;
        /**
         * @ORM\Column(type="boolean")
         */
        private $alreadyapplied;
        /**
         * @ORM\Column(type="boolean")
         */
        private $published = false;
        /**
         * @ORM\Column(type="boolean")
         */
        private $isexternal;
        
        public function __construct()
        {
            $this->views = 0;
            $this->datePositions = new ArrayCollection();
            $this->images = new ArrayCollection();
            $this->files = new ArrayCollection();
            $this->reference = $this->generateReference(10);
        }
        
        /**
         * @throws Exception
         */
        public function generateReference($length)
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
        public function getEvent()
        {
            return $this->event;
        }
        
        /**
         * @param mixed $event
         */
        public function setEvent($event): void
        {
            $this->event = $event;
        }
        
        /**
         * @return ArrayCollection
         */
        public function getDatePositions(): ArrayCollection
        {
            return $this->datePositions;
        }
        
        public function addDatePosition(ListingDatePosition $datePosition): self
        {
            if (!$this->datePositions->contains($datePosition)) {
                $this->datePositions[] = $datePosition;
                $datePosition->setListing($this);
            }
            
            return $this;
        }
        
        public function removeDatePosition(ListingDatePosition $datePosition): self
        {
            if ($this->datePositions->removeElement($datePosition)) {
                // set the owning side to null (unless already changed)
                if ($datePosition->getListing() === $this) {
                    $datePosition->setListing(null);
                }
            }
            
            return $this;
        }
        
        /**
         * @return mixed
         */
        public function getAlreadyapplied()
        {
            return $this->alreadyapplied;
        }
        
        /**
         * @param mixed $alreadyapplied
         */
        public function setAlreadyapplied($alreadyapplied): void
        {
            $this->alreadyapplied = $alreadyapplied;
        }
        
        /**
         * @return mixed
         */
        public function getTypeoflisting()
        {
            return $this->typeoflisting;
        }
        
        /**
         * @param mixed $typeoflisting
         */
        public function setTypeoflisting($typeoflisting): void
        {
            $this->typeoflisting = $typeoflisting;
        }
        
        /**
         * @return mixed
         */
        public function getIsexternal()
        {
            return $this->isexternal;
        }
        
        /**
         * @param mixed $isexternal
         */
        public function setIsexternal($isexternal): void
        {
            $this->isexternal = $isexternal;
        }
        
        /**
         * @return mixed
         */
        public function getShortdescription()
        {
            return $this->shortdescription;
        }
        
        /**
         * @param mixed $shortdescription
         */
        public function setShortdescription($shortdescription): void
        {
            $this->shortdescription = $shortdescription;
        }
        
        /**
         * @return bool
         */
        public function isPublished(): bool
        {
            return $this->published;
        }
        
        /**
         * @param bool $published
         */
        public function setPublished(bool $published): void
        {
            $this->published = $published;
        }
        
        /**
         * @return ArrayCollection
         */
        public function getFiles()
        {
            return $this->files;
        }
        
        /**
         * @param mixed $files
         */
        public function setFiles($files): void
        {
            $this->files = $files;
        }
        
        public function addFile(ListingFile $file)
        {
            if (!$this->files->contains($file)) {
                $this->files[] = $file;
                $file->setListingfile($this);
            }
        }
        
        public function removeFile(ListingFile $file)
        {
            if ($this->files->contains($file)) {
                $this->files->removeElement($file);
                // Set the owning side of the relationship to null
                $file->setListingfile(null);
            }
        }
        
        public function getDescription()
        {
            return $this->description;
        }
        
        public function setDescription($description)
        {
            $this->description = $description;
            
            return $this;
        }
        
        public function getCategory()
        {
            return $this->category;
        }
        
        public function setCategory(ListingCategory $category)
        {
            $this->category = $category;
            
            return $this;
        }
        
        
        /**
         * @param mixed $isPassed
         */
        public function setIsPassed($isPassed): void
        {
            $this->isPassed = $isPassed;
        }
        
        /**
         * @return bool
         */
        public function isAttnded(): bool
        {
            return $this->isAttnded;
        }
        
        /**
         * @param bool $isAttnded
         */
        public function setIsAttnded(bool $isAttnded): void
        {
            $this->isAttnded = $isAttnded;
        }
        
        public function getOrganizer()
        {
            return $this->organizer;
        }
        
        public function setOrganizer(Organizer $organizer)
        {
            $this->organizer = $organizer;
            
            return $this;
        }
        
        public function getReference()
        {
            return $this->reference;
        }
        
        public function setReference($reference)
        {
            $this->reference = $reference;
            
            return $this;
        }
        
        public function getViews()
        {
            return $this->views;
        }
        
        
        public function setViews($views)
        {
            $this->views = $views;
            
            return $this;
        }
        
        public function getExternallink()
        {
            return $this->externallink;
        }
        
        public function setExternallink($externallink)
        {
            $this->externallink = $externallink;
            
            return $this;
        }
        
        public function getPhonenumber()
        {
            return $this->phonenumber;
        }
        
        public function setPhonenumber($phonenumber)
        {
            $this->phonenumber = $phonenumber;
            
            return $this;
        }
        
        public function getEmail()
        {
            return $this->email;
        }
        
        public function setEmail($email)
        {
            $this->email = $email;
            
            return $this;
        }
        
        public function getTags()
        {
            return $this->tags;
        }
        
        public function setTags($tags)
        {
            $this->tags = $tags;
            
            return $this;
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
// It is required that at least one field changes if you are using doctrine
// otherwise the event listeners won't be called and the file is lost
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
        
        /**
         * @return ArrayCollection
         */
        public function getImages()
        {
            return $this->images;
        }
        
        public function getUpdatedAt()
        {
            return $this->updatedAt == $this->createdAt ? null : $this->updatedAt;
        }
        
        public function setUpdatedAt($updatedAt)
        {
            $this->updatedAt = $updatedAt;
            
            return $this;
        }
        
        public function addImage(ListingImage $image)
        {
            if (!$this->images->contains($image)) {
                $this->images[] = $image;
                $image->setListing($this);
            }
            
            return $this;
        }
        
        public function removeImage(ListingImage $image)
        {
            if ($this->images->contains($image)) {
                $this->images->removeElement($image);
                $image->setListing(null);
            }
        }
        
        public function getSlug()
        {
            return $this->slug ?: "";
        }
        
        public function setSlug($slug = null)
        {
            if ($slug === null) {
                $slug = $this->generateUniqueSlug();
            }
            $this->slug = $slug;
        }
        
        private function generateUniqueSlug()
        {
            $baseSlug = $this->getName();// Generate the base slug here, for example, from the title or some unique identifier.
            $salt = uniqid('', true); // Generate a unique salt, you can use other methods if needed.
            
            // Combine the base slug and salt to create a unique slug.
            // You can further sanitize or modify the uniqueSlug if needed.
            
            return $baseSlug . '-' . $salt;
        }
        
        public function getName()
        {
            return $this->name;
        }
        
        public function setName(string $name)
        {
            $this->name = $name;
            
            return $this;
        }
        
        public function getId()
        {
            return $this->id;
        }
        
        public function setId($id)
        {
            $this->id = $id;
        }
        
        public function viewed()
        {
            $this->views++;
        }
        
        public function getImagePath()
        {
            return 'uploads/listings/' . $this->imageName;
        }
        
        public function getImagePlaceholder($size = "default")
        {
//        if ($size == "small") {
//            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAV4AAAFeCAMAAAD69YcoAAAAyVBMVEUAAADd3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d0jcjdZAAAAQnRSTlMAAQIEBQYLDA8SExQWFxkaHyAhJCUmKi06O0lRVVZfYmNkb3V3g46PkZKbo6ivtLW5vL7FyMrT19na3Ojr7fHz9/luoWfVAAACeUlEQVR42u3cBXKDQBiAUaDukkrqSb1N3d3uf6jeoJbuBtj3nWD3zTDAvwxZJkmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJElSjD5qWxsvXrx48eLFixcvXrx48eLFixcvXrwl571oV7cK8JZiTTXaCl68eG0FrzXhxYsXL168ePHixYsXr62kwXuz/2WbeLvayjdd4sWLFy9evHjx4sWLFy9evHjx4sX7B96ir7zl1efdL/H30Qt48eLFixcvXrx48eLFixcvXrx48eKtF+9Yo7wNOK1wWoEXL168ePHixYsXL168ePHixYsXL94f8/7vOL3RFYjTCrx48eLFixcvXrx48eLFixcvXrx48XbBm/wHqGF5jdPx4sWLFy9evHjx4sWLFy9evHjx4sVrnI4XL168ePHixYsXL168ePHixYsXL168eI3T8eLFixcvXrx48eLFixcvXrx48eLFW07e3/1gYAJvyHF6By9evHjx4sWLFy9evHjx4sWLFy9evPXmHWv8Z/3mvdUNL168ePHixYsXbyV5L/CG5D3CG5L3AG9I3hbekLxNvCF5R/AG5H3N8Abk3YmzprePNJuOw/uQpu59pCuqkyZvMxLvepK6d3kk3pkkeedi3W7zpwR1d+M9zqykp3tbxOMtHlPTfRmO+TS+lJju+2Tct529pHSfJ+LqZsV1QrpXQ9Ff1gdOk9HdyrP45dtp4J6P92jaNHVWf9zj2R6O8+YPa217sjLY43lpMdVc3WjXrtba8uJokUmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmS1Js+AbSytGbYpVXAAAAAAElFTkSuQmCC";
//        }
//        return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABgAAAAYACAMAAACHMHNZAAABVlBMVEUAAADd3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d18zGJfAAAAcXRSTlMAAQIDBAUHCAkLDA0PEBESExQWFxocHiAjJCUmKywtLzA0NTY3ODk6Pj9AQUJHSU1OT1BYWV9jZGZoaWtsbW90dXd7fH5/goOFjpSXmJqeoKOlpqq1t7nBw8XKzs/T1dfZ3N7g4uTm6+3v8fP19/n7/XM0K4AAABCKSURBVHja7d1bbxR1HMfhBSe1EKkYUEAjkaioCWjwUmO4IF544ZXR97GvZN+FhlTlwqChRoyHiASCLaek0FLLSqEFSkuB3e62XTmESqHF0m6Znfk9zz1Rvo799N/OzBYKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAECrWxV9gKJrAFJTMkGqVpsAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQCg2RITLE/JBARWNIETAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAA9KTACkpZjyP7/kBACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAEBzJSZIV9EEpKhkAicAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAAmikxQbpKJgCcAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABABAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQAQAAAEAAABAEAAABAAAPIiMUG6iiZYlpL9U90PJwAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAADmSExAZCUT4AQAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgBA8yQmILJi8L9/ySXgBACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAEAzJSYgspIJcAIAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEA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";
            return 'https://universeacg.xyz/uploads/imgs/default.jpg';
        }
        
        public function getCreatedAt()
        {
            return $this->createdAt;
        }
        
        public function setCreatedAt($createdAt)
        {
            $this->createdAt = $createdAt;
            
            return $this;
        }
        
        public function getDeletedAt()
        {
            return $this->deletedAt;
        }
        
        public function setDeletedAt($deletedAt)
        {
            $this->deletedAt = $deletedAt;
            
            return $this;
        }
    }