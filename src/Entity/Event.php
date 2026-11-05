<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Entity;

    use App\Validator\Constraints\EnglishOnly;
    use App\Validator\Constraints\Profanity;
    use DateTime;
    use DateTimeImmutable;
    use DateTimeInterface;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\ORM\Mapping\JoinColumn;
    use Exception;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Symfony\Component\HttpFoundation\File\File;
    use Symfony\Component\Validator\Constraints as Assert;
    use Vich\UploaderBundle\Mapping\Annotation as Vich;

    #[ORM\Table(name: 'universe_event')]
    #[ORM\Entity(repositoryClass: \App\Repository\EventRepository::class)]
    #[Assert\Callback(['App\Validation\Validator', 'validateEvent'])]
    #[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
    #[Vich\Uploadable]
    class Event
    {

        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        protected $name;
        #[ORM\Column(type: 'text', nullable: true)]
        protected $description;
        #[Gedmo\Slug(fields: ['name'], updatable: false)]
        #[ORM\Column(length: 255, unique: true)]
        protected $slug;

//    * //         * @ORM\JoinTable(name="universe_volunteer_event")
        #[ORM\ManyToMany(targetEntity: \App\Entity\User::class, inversedBy: 'volunteerEvents')]
        private $volunteers;

        #[ORM\JoinTable(name: 'universe_volunteer_event_select_attendees')]
        #[ORM\ManyToMany(targetEntity: \App\Entity\User::class)]
        private $selectattendees;
        #[JoinColumn(name: 'venue_id', referencedColumnName: 'id')]
        #[ORM\ManyToOne(targetEntity: \App\Entity\Venue::class, inversedBy: 'events', fetch: 'LAZY')]
        private $venue;

        #[ORM\Column(type: 'boolean')]
        private $redirectable;

        #[ORM\Column(type: 'boolean')]
        private $allowoverbook;
        #[ORM\Column(type: 'boolean')]
        private $viewablebyselect;
        private $visibletoallmembers;
        private $addmembersasviewable;
        private $publishOnSpecificDate = false;
        private $repeatsweekly;
        private $repeatweeks;
        private $repeatstartdate;
        private $repeatenddate;
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private $id;
        #[ORM\ManyToOne(targetEntity: \App\Entity\Category::class, inversedBy: 'events', fetch: 'LAZY')]
        private $category;
        private $isPassed = false;
        private $isAttnded = false;
        #[ORM\ManyToOne(targetEntity: \App\Entity\Organizer::class, inversedBy: 'events', fetch: 'LAZY')]
        private $organizer;
        #[ORM\OneToMany(targetEntity: \App\Entity\EventImage::class, mappedBy: 'event', cascade: ['persist', 'remove'], orphanRemoval: true, fetch: 'LAZY')]
        private $images;
        #[ORM\Column(type: 'boolean')]
        private $outofcampus;
        #[ORM\OneToMany(targetEntity: \App\Entity\EventFile::class, mappedBy: 'event', cascade: ['persist', 'remove'], orphanRemoval: true, fetch: 'LAZY')]
        private $files;
        #[ORM\OneToMany(targetEntity: \App\Entity\EventDate::class, mappedBy: 'event', cascade: ['persist', 'remove'], fetch: 'LAZY', orphanRemoval: true)]
        #[ORM\OrderBy(['startdate' => 'ASC'])]
        #[Assert\Valid(groups: ['create', 'update'])]
        private $eventdates;
        private $eventDatesMulti;

        // Getters and setters...
        #[ORM\JoinTable(name: 'universe_favorites')]
        #[ORM\JoinColumn(name: 'Event_id', referencedColumnName: 'id')]
        #[ORM\InverseJoinColumn(name: 'User_Id', referencedColumnName: 'id')]
        #[ORM\ManyToMany(targetEntity: \App\Entity\User::class, inversedBy: 'favorites', cascade: ['persist'])]
        private $addedtofavoritesby;
        #[ORM\ManyToOne(targetEntity: \App\Entity\HomepageHeroSettings::class, inversedBy: 'events', cascade: ['persist'])]
        private $isonhomepageslider;
        /**
         * @var string
         */
        #[ORM\Column(type: 'string', length: 255)]
        private $reference;
        /**
         * @var integer
         */
        #[ORM\Column(type: 'integer')]
        private $views;
        /**
         * @var string
         *
         * @EnglishOnly
         * @Profanity
         */
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $externallink;
        /**
         * @var string
         *
         * @EnglishOnly
         * @Profanity
         */
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $phonenumber;
        /**
         * @var string
         *
         * @EnglishOnly
         * @Profanity
         */
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $email;
        /**
         * @var string
         *
         * @EnglishOnly
         * @Profanity
         */
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $tags;
        /**
         * NOTE: This is not a mapped field of entity metadata, just a simple property.
         *
         * @var File
         */
        #[Assert\File(maxSize: '32M', mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'], mimeTypesMessage: 'The file should be an image')]
        #[Vich\UploadableField(mapping: 'event_image', fileNameProperty: 'imageName', size: 'imageSize', mimeType: 'imageMimeType', originalName: 'imageOriginalName', dimensions: 'imageDimensions')]
        private $imageFile;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $imageName;
        /**
         *
         * @var integer
         */
        #[ORM\Column(type: 'integer', nullable: true)]
        private $imageSize;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $imageMimeType;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $commonvalue;
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $imageOriginalName;
        #[ORM\Column(type: 'simple_array', nullable: true)]
        private $imageDimensions;
        /**
         * @var DateTime $createdAt
         */
        #[Gedmo\Timestampable(on: 'create')]
        #[ORM\Column(type: 'datetime')]
        private $createdAt;
        /**
         * @var DateTime $updatedAt
         */
        #[Gedmo\Timestampable(on: 'update')]
        #[ORM\Column(type: 'datetime', nullable: true)]
        private $updatedAt;
        #[ORM\Column(type: 'datetime', nullable: true)]
        private $deletedAt;
        #[ORM\Column(type: 'boolean')]
        private $published = false;
        #[ORM\Column(type: 'boolean')]
        private $open;
        private $repeatDays;
        #[ORM\Column(type: 'boolean')]
        private $isrepeat = false;
        #[ORM\Column(type: 'boolean')]
        #[Assert\NotNull(groups: ['create', 'update'])]
        private $showattendees = true;
        private $isattended = false;
        #[ORM\Column(type: 'boolean')]
        #[Assert\NotNull(groups: ['create', 'update'])]
        private $volsneeded = false;
        #[ORM\Column(type: 'datetime', nullable: true, name: 'publish_date')]
        private $publishDate;

        public function __construct()
        {
            $this->eventDatesMulti = new ArrayCollection();
            $this->views = 0;
            $this->open = true;
            $this->repeatsweekly = false;
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
        public function getAllowoverbook()
        {
            return $this->allowoverbook;
        }

        /**
         * @param mixed $allowoverbook
         */
        public function setAllowoverbook($allowoverbook): void
        {
            $this->allowoverbook = $allowoverbook;
        }

        /**
         * @return mixed
         */
        public function getRedirectable()
        {
            return $this->redirectable;
        }

        /**
         * @param mixed $redirectable
         */
        public function setRedirectable($redirectable): void
        {
            $this->redirectable = $redirectable;
        }

        /**
         * @return mixed
         */
        public function getSelectattendees()
        {
            return $this->selectattendees;
        }

        /**
         * @param mixed $selectattendees
         */
        public function setSelectattendees($selectattendees): void
        {
            $this->selectattendees = $selectattendees;
        }

        /**
         * @return mixed
         */
        public function getVisibletoallmembers()
        {
            return $this->visibletoallmembers;
        }

        /**
         * @param mixed $visibletoallmembers
         */
        public function setVisibletoallmembers($visibletoallmembers): void
        {
            $this->visibletoallmembers = $visibletoallmembers;
        }

        /**
         * @return mixed
         */
        public function getAddmembersasviewable()
        {
            return $this->addmembersasviewable;
        }

        /**
         * @param mixed $addmembersasviewable
         */
        public function setAddmembersasviewable($addmembersasviewable): void
        {
            $this->addmembersasviewable = $addmembersasviewable;
        }

        /**
         * @return mixed
         */
        public function getViewablebyselect()
        {
            return $this->viewablebyselect;
        }

        /**
         * @param mixed $viewablebyselect
         */
        public function setViewablebyselect($viewablebyselect): void
        {
            $this->viewablebyselect = $viewablebyselect;
        }

        public function hasvolunters()
        {
            return $this->volunteers && count($this->volunteers) > 0;
        }

        /**
         * Add a volunteer to the event.
         *
         * @param User $volunteer
         */
        public function addVolunteer(User $volunteer)
        {
            // Check if the volunteer is not already associated with the event
            if (!$this->volunteers->contains($volunteer)) {
                $this->volunteers->add($volunteer);
                // You may also want to update the inverse side of the relationship
                $volunteer->addVolunteerEvent($this);
            }
        }

        /**
         * Remove a volunteer from the event.
         *
         * @param User $volunteer
         */
        public function removeVolunteer(User $volunteer)
        {
            if ($this->volunteers->contains($volunteer)) {
                $this->volunteers->removeElement($volunteer);
                // You may also want to update the inverse side of the relationship
                $volunteer->removeVolunteerEvent($this);
            }
        }

        /**
         * @return bool
         */
        public function isIsattended(): bool
        {
            return $this->isattended;
        }

        /**
         * @param bool $isattended
         */
        public function setIsattended(bool $isattended): void
        {
            $this->isattended = $isattended;
        }

        /**
         * @return mixed
         */
        public function getVolunteers()
        {
            return $this->volunteers;
        }

        /**
         * @param mixed $volunteers
         */
        public function setVolunteers($volunteers): void
        {
            $this->volunteers = $volunteers;
        }

        /**
         * @return ArrayCollection|EventFile[]
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

        public function addFile(EventFile $file)
        {
            if (!$this->files->contains($file)) {
                $this->files[] = $file;
                $file->setEvent($this);
            }
        }

        public function removeFile(EventFile $file)
        {
            if ($this->files->contains($file)) {
                $this->files->removeElement($file);
                // Set the owning side of the relationship to null
                $file->setEvent(null);
            }
        }

        /**
         * @return mixed
         */
        public function getOutofcampus()
        {
            return $this->outofcampus;
        }

        /**
         * @param mixed $outofcampus
         */
        public function setOutofcampus($outofcampus): void
        {
            $this->outofcampus = $outofcampus;
        }

        // ... Other getter and setter methods

        public function getVenue()
        {
            return $this->venue;
        }

        public function setVenue($venue)
        {
            $this->venue = $venue;
        }

        public function getPublishDate(): ?DateTimeInterface
        {
            return $this->publishDate;
        }

        public function setPublishDate(?DateTimeInterface $publishDate): self
        {
            $this->publishDate = $publishDate;

            return $this;
        }

        public function isPublishOnSpecificDate(): bool
        {
            return $this->publishOnSpecificDate;
        }

        public function setPublishOnSpecificDate(bool $publishOnSpecificDate): self
        {
            $this->publishOnSpecificDate = $publishOnSpecificDate;

            return $this;
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
                if (($eventDate->getEnddate() ?? $eventDate->getStartdate()) > new DateTime()) {
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
         * @return ArrayCollection
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
            return $this->getLatestEndDate() >= new DateTime();
        }

        public function getLatestEndDate(): ?DateTime
        {
            $latestEndDate = null;

            // Assuming you have a property like $eventDates that holds the collection of EventDates
            /** @var Collection|EventDate[] $eventDates */
            $eventDates = $this->getEventdates();

            foreach ($eventDates as $eventDate) {
                $endDate = $eventDate->getEnddate();

                if ($endDate !== null) {
                    if ($latestEndDate === null || $endDate > $latestEndDate) {
                        $latestEndDate = $endDate;
                    }
                }
            }

            return $latestEndDate;
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

        public function isOnSale()
        {
            return
                $this->hasAnEventDateOnSale() &&
                $this->organizer->getUser()->isEnabled() &&
                $this->published;
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
//        if ($size == "small") {
//            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAV4AAAFeCAMAAAD69YcoAAAAyVBMVEUAAADd3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d0jcjdZAAAAQnRSTlMAAQIEBQYLDA8SExQWFxkaHyAhJCUmKi06O0lRVVZfYmNkb3V3g46PkZKbo6ivtLW5vL7FyMrT19na3Ojr7fHz9/luoWfVAAACeUlEQVR42u3cBXKDQBiAUaDukkrqSb1N3d3uf6jeoJbuBtj3nWD3zTDAvwxZJkmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJElSjD5qWxsvXrx48eLFixcvXrx48eLFixcvXrwl571oV7cK8JZiTTXaCl68eG0FrzXhxYsXL168ePHixYsXr62kwXuz/2WbeLvayjdd4sWLFy9evHjx4sWLFy9evHjx4sX7B96ir7zl1efdL/H30Qt48eLFixcvXrx48eLFixcvXrx48eKtF+9Yo7wNOK1wWoEXL168ePHixYsXL168ePHixYsXL94f8/7vOL3RFYjTCrx48eLFixcvXrx48eLFixcvXrx48XbBm/wHqGF5jdPx4sWLFy9evHjx4sWLFy9evHjx4sVrnI4XL168ePHixYsXL168ePHixYsXL168eI3T8eLFixcvXrx48eLFixcvXrx48eLFW07e3/1gYAJvyHF6By9evHjx4sWLFy9evHjx4sWLFy9evPXmHWv8Z/3mvdUNL168ePHixYsXbyV5L/CG5D3CG5L3AG9I3hbekLxNvCF5R/AG5H3N8Abk3YmzprePNJuOw/uQpu59pCuqkyZvMxLvepK6d3kk3pkkeedi3W7zpwR1d+M9zqykp3tbxOMtHlPTfRmO+TS+lJju+2Tct529pHSfJ+LqZsV1QrpXQ9Ff1gdOk9HdyrP45dtp4J6P92jaNHVWf9zj2R6O8+YPa217sjLY43lpMdVc3WjXrtba8uJokUmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmS1Js+AbSytGbYpVXAAAAAAElFTkSuQmCC";
//        }
//        return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABgAAAAYACAMAAACHMHNZAAABVlBMVEUAAADd3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d18zGJfAAAAcXRSTlMAAQIDBAUHCAkLDA0PEBESExQWFxocHiAjJCUmKywtLzA0NTY3ODk6Pj9AQUJHSU1OT1BYWV9jZGZoaWtsbW90dXd7fH5/goOFjpSXmJqeoKOlpqq1t7nBw8XKzs/T1dfZ3N7g4uTm6+3v8fP19/n7/XM0K4AAABCKSURBVHja7d1bbxR1HMfhBSe1EKkYUEAjkaioCWjwUmO4IF544ZXR97GvZN+FhlTlwqChRoyHiASCLaek0FLLSqEFSkuB3e62XTmESqHF0m6Znfk9zz1Rvo799N/OzBYKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAECrWxV9gKJrAFJTMkGqVpsAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQCg2RITLE/JBARWNIETAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAA9KTACkpZjyP7/kBACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAEBzJSZIV9EEpKhkAicAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAAmikxQbpKJgCcAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABABAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQAQAAAEAAABAEAAABAAAPIiMUG6iiZYlpL9U90PJwAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAADmSExAZCUT4AQAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgBA8yQmILJi8L9/ySXgBACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAEAzJSYgspIJcAIAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEA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";
            return 'https://universe.alexandroskourtis.me/uploads/imgs/default.jpg';
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
