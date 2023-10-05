<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
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
     * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
     * @ORM\Table(name="universe_event")
     * @Assert\Callback({"App\Validation\Validator", "validateEvent"})
     * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
     * @Vich\Uploadable
     */
    class Event
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
         * @Gedmo\Slug(fields={"name"}, updatable=false)
         * @ORM\Column(length=255, unique=true)
         */
        protected $slug;
        private $repeatsweekly;
        
        private $repeatweeks;
        private $repeatstartdate;
        private $repeatenddate;
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;
        /**
         * @ORM\ManyToOne(targetEntity="Category", inversedBy="events")
         */
        private $category;
        private $isPassed = false;
        private $isAttnded = false;
        /**
         * @ORM\ManyToOne(targetEntity="Organizer", inversedBy="events")
         */
        private $organizer;
        /**
         * @ORM\OneToMany(targetEntity="EventImage", mappedBy="event", cascade={"persist", "remove"}, orphanRemoval=true, fetch = "EAGER")
         */
        private $images;
        /**
         * @ORM\OneToMany(targetEntity="EventDate", mappedBy="event", cascade={"persist", "remove"}, fetch = "EAGER", orphanRemoval=true)
         * @ORM\OrderBy({"startdate" = "ASC"})
         * @Assert\Valid(groups={"create", "update"})
         */
        private $eventdates;
        private $eventDatesMulti;
        /**
         * @ORM\ManyToMany(targetEntity="User", inversedBy="favorites", cascade={"persist", "merge"})
         * @ORM\JoinTable(name="universe_favorites",
         *   joinColumns={@ORM\JoinColumn(name="Event_id", referencedColumnName="id")},
         *   inverseJoinColumns={@ORM\JoinColumn(name="User_Id", referencedColumnName="id")}
         * )
         */
        private $addedtofavoritesby;
        /**
         * @ORM\ManyToOne(targetEntity="HomepageHeroSettings", inversedBy="events", cascade={"persist"})
         */
        private $isonhomepageslider;
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
         */
        private $externallink;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $phonenumber;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $email;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $tags;
        /**
         * NOTE: This is not a mapped field of entity metadata, just a simple property.
         *
         * @Vich\UploadableField(mapping="event_image", fileNameProperty="imageName", size="imageSize", mimeType="imageMimeType", originalName="imageOriginalName", dimensions="imageDimensions")
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
        private $commonvalue;
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
        private $published = false;
        /**
         * @ORM\Column(type="boolean")
         */
        private $open = false;
        private $repeatDays;
        /**
         * @ORM\Column(type="boolean")
         */
        private $isrepeat = false;
        /**
         * @ORM\Column(type="boolean")
         * @Assert\NotNull(groups={"create", "update"})
         */
        private $showattendees = true;
        /**
         * @ORM\Column(type="boolean")
         * @Assert\NotNull(groups={"create", "update"})
         */
        private $volsneeded = false;
        
        public function __construct()
        {
            $this->eventDatesMulti = new ArrayCollection();
            $this->views = 0;
            $this->eventdates = new ArrayCollection();
            $this->images = new ArrayCollection();
            $this->reference = $this->generateReference(10);
            $this->addedtofavoritesby = new ArrayCollection();
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
        public function getRepeatweeks()
        {
            return $this->repeatweeks;
        }
        
        /**
         * @param mixed $repeatweeks
         */
        public function setRepeatweeks($repeatweeks): void
        {
            $this->repeatweeks = $repeatweeks;
        }
        
        /**
         * @return mixed
         */
        public function getRepeatstartdate()
        {
            return $this->repeatstartdate;
        }
        
        /**
         * @param mixed $repeatstartdate
         */
        public function setRepeatstartdate($repeatstartdate): void
        {
            $this->repeatstartdate = $repeatstartdate;
        }
        
        /**
         * @return mixed
         */
        public function getRepeatenddate()
        {
            return $this->repeatenddate;
        }
        
        /**
         * @param mixed $repeatenddate
         */
        public function setRepeatenddate($repeatenddate): void
        {
            $this->repeatenddate = $repeatenddate;
        }
        
        /**
         * @return mixed
         */
        public function getRepeatsweekly()
        {
            return $this->repeatsweekly;
        }
        
        /**
         * @param mixed $repeatsweekly
         */
        public function setRepeatsweekly($repeatsweekly): void
        {
            $this->repeatsweekly = $repeatsweekly;
        }
        
        public function mapFromEvent(Event $oldEvent)
        {
            // Copy properties from the old event to the current instance
            $this->setName($oldEvent->getName());
            $this->setDescription($oldEvent->getDescription());
            $this->setSlug($this->generateUniqueSlug());
            $this->setCategory($oldEvent->getCategory());
            $this->setIsPassed($oldEvent->getIsPassed());
            $this->setIsAttnded($oldEvent->isAttnded());
            $this->setOrganizer($oldEvent->getOrganizer());
            $this->setReference($oldEvent->getReference());
            $this->setViews($oldEvent->getViews());
            $this->setExternalLink($oldEvent->getExternalLink());
            $this->setPhoneNumber($oldEvent->getPhoneNumber());
            $this->setEmail($oldEvent->getEmail());
            $this->setTags($oldEvent->getTags());
            $this->setCommonValue($oldEvent->getCommonValue());
            $this->setPublished($oldEvent->getPublished());
            $this->setOpen($oldEvent->getOpen());
            $this->setRepeatDays($oldEvent->getRepeatDays());
            $this->setIsrepeat($oldEvent->isIsrepeat());
            $this->setShowattendees($oldEvent->getShowattendees());
            $this->setVolsneeded($oldEvent->isVolsneeded());
            $this->setImageFile($oldEvent->getImageFile());
            $this->setImageName($oldEvent->getImageName());
            $this->setImageSize($oldEvent->getImageSize());
            $this->setImageMimeType($oldEvent->getImageMimeType());
            $this->setImageOriginalName($oldEvent->getImageOriginalName());
            $this->setImageDimensions($oldEvent->getImageDimensions());
            
            // Copy images from the old event (assuming $images is a collection)
            foreach ($oldEvent->getImages() as $image) {
                $newImage = new EventImage();
                $newImage->setImageFile($image->getImageFile());
                $newImage->setImageName($image->getImageName());
                $newImage->setImageSize($image->getImageSize());
                $newImage->setImageMimeType($image->getImageMimeType());
                $newImage->setImageOriginalName($image->getImageOriginalName());
                $newImage->setImageDimensions($image->getImageDimensions());
                $newImage->setPosition($image->getPosition());
                $newImage->setUpdatedAt($image->getUpdatedAt());
                $newImage->setEvent($this);
                $this->addImage($newImage);
            }
            
            // Copy event dates (assuming $eventdates is a collection)
            foreach ($oldEvent->getEventDates() as $eventDate) {
                $newEventDate = new EventDate();
                $newEventDate->setOnline($eventDate->getOnline());
                foreach ($eventDate->getTickets() as $oldticket) {
                    $newEventTicket = new EventTicket();
                    $newEventTicket->setAlreadyBooked($oldticket->isAlreadyBooked());
                    $newEventTicket->setEventDate(null); // You may need to set this association
                    $newEventTicket->setActive($oldticket->getActive());
                    $newEventTicket->setReference($oldticket->getReference());
                    $newEventTicket->setName($oldticket->getName());
                    $newEventTicket->setDescription($oldticket->getDescription());
                    $newEventTicket->setQuantity($oldticket->getQuantity());
                    $newEventTicket->setTicketsPerAttendee($oldticket->getTicketsPerAttendee());
                    $newEventTicket->setSalesStartdate($oldticket->getSalesStartdate());
                    $newEventTicket->setSalesEnddate($oldticket->getSalesEnddate());
                    $newEventTicket->setPosition($oldticket->getPosition());
                    $newEventDate->addTicket($newEventTicket);
                }
                $newEventDate->setActive($eventDate->getActive());
                $newEventDate->setReference($eventDate->getReference());
                $newEventDate->setDescription($eventDate->getDescription());
                $newEventDate->setTitle($eventDate->getTitle());
                $newEventDate->setStartdate($eventDate->getStartdate());
                $newEventDate->setEnddate($eventDate->getEnddate());
                $this->addEventDate($newEventDate);
            }
            
            // Ensure that the new event has its own unique properties set
            $this->setId(null); // Reset the ID to create a new entity
            $this->setReference($this->generateReference(10)); // Generate a new reference
            $this->setViews(0); // Reset views count
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
        
        public function getDescription()
        {
            return $this->description;
        }
        
        public function setDescription($description)
        {
            $this->description = $description;
            
            return $this;
        }
        
        private function generateUniqueSlug()
        {
            $baseSlug = $this->getName();// Generate the base slug here, for example, from the title or some unique identifier.
            $salt = uniqid('', true); // Generate a unique salt, you can use other methods if needed.
            
            // Combine the base slug and salt to create a unique slug.
            // You can further sanitize or modify the uniqueSlug if needed.
            
            return $baseSlug . '-' . $salt;
        }
        
        public function getCategory()
        {
            return $this->category;
        }
        
        public function setCategory(Category $category)
        {
            $this->category = $category;
            
            return $this;
        }
        
        /**
         * @return bool
         */
        public function getIsPassed()
        {
            // Check if all event dates are in the past
            foreach ($this->eventdates as $eventDate) {
                if ($eventDate->getEnddate() > new DateTime()) {
                    return false; // At least one event date is in the future
                }
            }
            
            return true; // All event dates are in the past
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
        
        /**
         * @return mixed
         */
        public function getCommonvalue()
        {
            return $this->commonvalue;
        }
        
        /**
         * @param mixed $commonvalue
         */
        public function setCommonvalue($commonvalue): void
        {
            $this->commonvalue = $commonvalue;
        }
        
        public function getPublished()
        {
            return $this->published;
        }
        
        public function setPublished($published)
        {
            $this->published = $published;
            
            return $this;
        }
        
        /**
         * @return false
         */
        public function getOpen()
        {
            return $this->open;
        }
        
        /**
         * @param mixed $open
         */
        public function setOpen($open): void
        {
            $this->open = $open;
        }
        
        public function getRepeatDays(): ?int
        {
            return $this->repeatDays;
        }
        
        public function setRepeatDays(?int $repeatDays): self
        {
            $this->repeatDays = $repeatDays;
            
            return $this;
        }
        
        /**
         * @return bool
         */
        public function isIsrepeat(): bool
        {
            return $this->isrepeat;
        }
        
        /**
         * @param bool $isrepeat
         */
        public function setIsrepeat(bool $isrepeat): void
        {
            $this->isrepeat = $isrepeat;
        }
        
        public function getShowattendees()
        {
            return $this->showattendees;
        }
        
        public function setShowattendees($showattendees)
        {
            $this->showattendees = $showattendees;
            
            return $this;
        }
        
        /**
         * @return bool
         */
        public function isVolsneeded(): bool
        {
            return (bool)$this->volsneeded;
        }
        
        /**
         * @param bool $volsneeded
         */
        public function setVolsneeded(bool $volsneeded): void
        {
            $this->volsneeded = $volsneeded;
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
        
        public function addImage($image)
        {
            if (!$this->images->contains($image)) {
                $this->images[] = $image;
                $image->setEvent($this);
            }
            
            return $this;
        }
        
        /**
         * @return ArrayCollection
         */
        public function getEventdates()
        {
            return $this->eventdates;
        }
        
        public function setEventdates($neweventdates)
        {
            $this->eventdates = $neweventdates;
        }
        
        public function addEventdate($eventdate)
        {
            if (!$this->eventdates->contains($eventdate)) {
                $this->eventdates[] = $eventdate;
                $eventdate->setEvent($this);
            }
            
            return $this;
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
        
        public function getId()
        {
            return $this->id;
        }
        
        public function setId($id)
        {
            $this->id = $id;
        }
        
        /**
         * @return mixed
         */
        public function getEventDatesMulti()
        {
            return $this->eventDatesMulti;
        }
        
        /**
         * @param mixed $eventDatesMulti
         */
        public function setEventDatesMulti($eventDatesMulti): void
        {
            $this->eventDatesMulti = $eventDatesMulti;
        }
        
        public function getTotalOrderElementsQuantitySum($status = 1, $user = "all", $role = "all")
        {
            $sum = 0;
            foreach ($this->eventdates as $eventDate) {
                $sum += $eventDate->getOrderElementsQuantitySum($status, $user, $role);
            }
            return $sum;
        }
        
        public function getTotalSalesPercentage()
        {
            if (count($this->eventdates) == 0)
                return 0;
            $eventDatesSalesPercentageSum = 0;
            foreach ($this->eventdates as $eventDate) {
                $eventDatesSalesPercentageSum += $eventDate->getTotalSalesPercentage();
            }
            return round($eventDatesSalesPercentageSum / count($this->eventdates));
        }
        
        public function isAddedToFavoritesBy($user)
        {
            return $this->addedtofavoritesby->contains($user);
        }
        
        public function stringifyStatus()
        {
            if (!$this->organizer->getUser()->isEnabled()) {
                return "Organizer is disabled";
            }
            
            if (!$this->published) {
                return "Event is not published";
            }
            
            return "";
        }
        
        public function stringifyStatusClass()
        {
            if (!$this->organizer->getUser()->isEnabled()) {
                return "danger";
            }
            
            if (!$this->published) {
                return "warning";
            }
            
            if (!$this->hasAnEventDateOnSale()) {
                return "info";
            }
            
            return "success";
        }
        
        public function hasAnEventDateOnSale(): bool
        {
            foreach ($this->eventdates as $eventdate) {
                if ($eventdate->isOnSale()) {
                    return true;
                }
            }
            return true;
        }
        
        public function isOnSale()
        {
            return
                $this->hasAnEventDateOnSale() &&
                $this->organizer->getUser()->isEnabled() &&
                $this->published;
        }
        
        public function hasContactAndSocialMedia()
        {
            return ($this->externallink || $this->phonenumber || $this->email);
        }
        
        public function viewed()
        {
            $this->views++;
        }
        
        public function hasTwoOrMoreEventDatesOnSale()
        {
            $count = 0;
            foreach ($this->eventdates as $eventdate) {
                if ($eventdate->isOnSale()) {
                    $count++;
                }
            }
            return $count >= 2;
        }
        
        public function getFirstOnSaleEventDate()
        {
            foreach ($this->eventdates as $eventdate) {
                if ($eventdate->isOnSale()) {
                    return $eventdate;
                }
            }
            return null;
        }
        
        public function getImagePath()
        {
            return 'uploads/events/' . $this->imageName;
        }
        
        public function getImagePlaceholder($size = "default")
        {
            if ($size == "small") {
                return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAABGCAMAAABG8BK2AAAAXVBMVEUAAAD2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhHkayjUAAAAHnRSTlMABQ0PEBEcKixARkdJSk1WYmRmZ3R1eHl7f4WjsMpiv/ZRAAAAtUlEQVRYw+3X2Q7CIBCFYVqRLmoH3Oly3v8xvfCiJrYsSjRp578l+RJIIIMQHLfEMiKiYmKhICLKQhkJAHZiwQKAXC2zPWjTAsBg3hsAoDW6zj3KDWEdncoZSOBsEJ5jX/sIpp5ndASj5xkTwRhm/s101lcXwijv9VXMMPMzRlZj5edM9XKTemaYWTNT9mNXfm++YzQANMpX45m2Es1+iSZRcQlWTs7TuydRnn8GX3qXC45bXA+ADIuZ4XkIYQAAAABJRU5ErkJggg==";
            }
            
            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAV4AAAFeCAMAAAD69YcoAAAA9lBMVEUAAAD2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhEZWD6+AAAAUXRSTlMAAQIDBAYJDQ4QExQVFhcYGhseHyAhKi0uLzAxMjQ6Ozw+P0BERUZHSUpPUVVWV1hZYmRmcXN0dYOFiYuXmJq3ur7HyNHT1dfZ3Obo8fX3+fvgzUWiAAADeklEQVR42u3cSVNTQRSA0RcBgQhOKCg4MImoJPKCA+BAQGVUof//n3Gpm7iQCt3ver51qjo5i5fu26lUlSRJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRdVdc/DGjrChbfGrT49Si8k2lA+1ew+P6gxSfx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjHbj5a79S93+0M+oSnveF3OmjxnT9eVG+szbWvNcD23puj1NT2OuNF246+PkvNrj9bLG6r+yM1v4O7Zere/ppitD1SoO7LFKbjdnG6uylQF/OFPXY/pVitFsX7MSW+Q+tdilc5z4enAXXTRSnfbxPnEXnTcSH7s36K2XYRuk9S1Eo4v7XOwvIeFMC7nOJWwHznMDBvP7vuVIpc9vlvLzRvJzfvQWjevdz3Eyl2me/fpoPzZj4ZzwfnncvLux6cdy0vbzc470Ze3jo4b23bO8x6ePHixYsXL168ePHixYsXL168ePHixYsXL168ePHixYsXL168ePHixYsXL168ePHixYsXL168/8J7st+kTprGu1g1qUW8ePHixYsXL168ePHixYsXL168ePHixYsXL168ePHixYsXL168ePHixYsXL168ePHixYsXL168ePHixYsXL168ePHixYsXL168eIfQ5MplWsL791Yu9c8453jx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjx4sWLFy9evHjxNp136fwyfcebJbx48eLFixcvXrx48eLFixcvXrx48eLFixcvXrx48eLFixfvf8BbD3pfu4tNanfQx6jz8nZT7Dby8q4H513LyzsfnPdBXt7p4LztvLyjwXmvZd7SfAmtu5d7x9gLzdvJzTsVmnc8+4HnMLBuP/95cjkw72x+3tZZWN0vJYxDnoblnSli3NQPqrtTxjRv4jyk7vFI6dPSJnfRrkrpfUDehYKm/Z/C6a6WdJnS+hxM91lht1UfQj13F6rSehVoz9CuyuvOtyj73ZGqxFqbPyOchGeqUhvdbPoAoj9bFd39t0fNte1ONOAnL2O3Hr/o1r0mVXeeP7wxUkmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEnSsPoFIVJb/voL1VsAAAAASUVORK5CYII=";
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
        
        public function getYoutubeurl()
        {
            return $this->youtubeurl;
        }
        
        public function setYoutubeurl($youtubeurl)
        {
            $this->youtubeurl = $youtubeurl;
            
            return $this;
        }
        
        public function removeEventdate($eventdate)
        {
            if ($this->eventdates->contains($eventdate)) {
                $this->eventdates->removeElement($eventdate);
// set the owning side to null (unless already changed)
                if ($eventdate->getEvent() === $this) {
                    $eventdate->setEvent(null);
                }
            }
            
            return $this;
        }
        
        public function removeImage($image)
        {
            if ($this->images->contains($image)) {
                $this->images->removeElement($image);
// set the owning side to null (unless already changed)
                if ($image->getEvent() === $this) {
                    $image->setEvent(null);
                }
            }
            
            return $this;
        }
        
        public function getArtists()
        {
            return $this->artists;
        }
        
        public function setArtists($artists)
        {
            $this->artists = $artists;
            
            return $this;
        }
        
        public function getYear()
        {
            return $this->year;
        }
        
        public function setYear($year)
        {
            $this->year = $year;
            
            return $this;
        }
        
        /**
         * @return ArrayCollection
         */
        public function getAddedtofavoritesby()
        {
            return $this->addedtofavoritesby;
        }
        
        public function addAddedtofavoritesby($addedtofavoritesby)
        {
            if (!$this->addedtofavoritesby->contains($addedtofavoritesby)) {
                $this->addedtofavoritesby[] = $addedtofavoritesby;
            }
            
            return $this;
        }
        
        public function removeAddedtofavoritesby($addedtofavoritesby)
        {
            if ($this->addedtofavoritesby->contains($addedtofavoritesby)) {
                $this->addedtofavoritesby->removeElement($addedtofavoritesby);
            }
            
            return $this;
        }
        
        public function getIsonhomepageslider()
        {
            return $this->isonhomepageslider;
        }
        
        public function setIsonhomepageslider($isonhomepageslider)
        {
            $this->isonhomepageslider = $isonhomepageslider;
            
            return $this;
        }
    }