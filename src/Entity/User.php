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
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="universe_user")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Vich\Uploadable
 */
class User extends BaseUser
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;
    /**
     * @ORM\Column(type="float")
     */
    protected $points;
    /**
     * @Gedmo\Slug(fields={"username"}, updatable=true)
     * @ORM\Column(length=255, unique=true)
     */
    protected $slug;
    /**
     * @ORM\Column(length=255)
     */
    protected $studentid;
    /**
     * @ORM\OneToOne(targetEntity="CV", mappedBy="user", cascade={"persist", "remove"})
     */
    private $cv;

    /**
     * @ORM\OneToOne(targetEntity="DocDocumentDTO", mappedBy="user", cascade={"persist", "remove"})
     */
    private $doc;
    /**
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="volunteers")
     */
    private $volunteerEvents;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\AthleticBooking", mappedBy="users")
     */
    private $athleticbookings;
    private $getcreatorreminder;
    private $getdailyreminders;
    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $bio;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $website;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(min = 1)
     */
    private $phone;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(max = 100)
     */
    private $facebook;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(max = 100)
     */
    private $tiktok;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(max = 100)
     */
    private $twitter;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(max = 100)
     */
    private $instagram;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(max = 100)
     */
    private $linkedin;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(max = 255)
     */
    private $youtubeurl;
    private $attendedevents;
    private $attendedeventscount;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull
     */
    private $isbusinessweek;
    /**
     * @ORM\Column(type="boolean")
     */
    private $nolevel;
    /**
     * @ORM\Column(type="boolean")
     */
    private $sendsms;
    /**
     * @ORM\Column(type="boolean")
     */
    private $specialuser;
    /**
     * @ORM\Column(type="boolean")
     */
    private $showfavs;
    /**
     * @ORM\Column(type="boolean")
     */
    private $showattendeed;
    /**
     * @ORM\Column(type="boolean")
     */
    private $showcomments;
    /**
     * @ORM\OneToOne(targetEntity="Organizer", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Valid()
     */
    private $organizer;
    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="user", cascade={"remove"})
     */
    private $orders;
    /**
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="addedtofavoritesby", fetch="LAZY", cascade={"remove"})
     */
    private $favorites;
    /**
     * @ORM\ManyToMany(targetEntity="Organizer", mappedBy="followedby", fetch="LAZY", cascade={"remove"})
     */
    private $following;
    /**
     * @ORM\ManyToOne(targetEntity="HomepageHeroSettings", inversedBy="organizers")
     */
    private $isorganizeronhomepageslider;
    /**
     * @ORM\OneToMany(targetEntity="TicketReservation", mappedBy="user", cascade={"remove"})
     */
    private $ticketreservations;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $firstname;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $lastname;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $street;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $street2;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $city;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $state;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $postalcode;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatarName", size="avatarSize", mimeType="avatarMimeType", originalName="avatarOriginalName", dimensions="avatarDimensions")
     * @Assert\File(
     *     maxSize = "2M",
     *     mimeTypes = {"image/jpeg", "image/jpg", "image/gif", "image/png"},
     *     mimeTypesMessage = "The file should be an image"
     *     )
     * @var File
     */
    private $avatarFile;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarName;
    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $avatarSize;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarMimeType;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarOriginalName;
    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $avatarDimensions;
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
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $apiKey;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="organizer_logo", fileNameProperty="logoName", size="logoSize", mimeType="logoMimeType", originalName="logoOriginalName", dimensions="logoDimensions")
     * @Assert\File(
     *     maxSize = "2M",
     *     mimeTypes = {"image/jpeg", "image/jpg", "image/gif", "image/png"},
     *     mimeTypesMessage = "The file should be an image"
     *     )
     * @var File
     */
    private $logoFile;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoName;
    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $logoSize;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoMimeType;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoOriginalName;
    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $logoDimensions;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="organizer_cover", fileNameProperty="coverName", size="coverSize", mimeType="coverMimeType", originalName="coverOriginalName", dimensions="coverDimensions")
     * @Assert\File(
     *     maxSize = "2M",
     *     mimeTypes = {"image/jpeg", "image/jpg", "image/gif", "image/png"},
     *     mimeTypesMessage = "The file should be an image"
     *     )
     * @var File
     */
    private $coverFile;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverName;
    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $coverSize;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverMimeType;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverOriginalName;
    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $coverDimensions;
    private $hideattending;
    private $hideleaderboards;
    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true, name="selectedimage")
     */
    private $selectedImage;
    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="following",  fetch = "EAGER", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="universe_following",
     *   joinColumns={@ORM\JoinColumn(name="Follow_To", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="Follow_From", referencedColumnName="id")}
     * )
     */
    private $followedby;
    private $followerscount;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull
     */
    private $showfollowers = true;

    public function __construct()
    {
        parent::__construct();
        $this->points = 0;
        $this->followedby = new ArrayCollection();
        $this->showattendeed = false;
        $this->getcreatorreminder = true;
        $this->getdailyreminders = true;
        $this->isbusinessweek = true;
        $this->showfavs = false;
        $this->showcomments = false;
        $this->orders = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->ticketreservations = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getFollowerscount()
    {
        return $this->followerscount;
    }

    /**
     * @param mixed $followerscount
     */
    public function setFollowerscount($followerscount): void
    {
        $this->followerscount = $followerscount;
    }

    public function isFollowedBy($user)
    {
        return $this->followedby->contains($user);
    }

    /**
     * @return ArrayCollection
     */
    public function getFollowedby()
    {
        return $this->followedby;
    }

    public function getShowfollowers()
    {
        return $this->showfollowers;
    }

    public function setShowfollowers($showfollowers)
    {
        $this->showfollowers = $showfollowers;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @param mixed $doc
     */
    public function setDoc($doc): void
    {
        $this->doc = $doc;
    }

    /**
     * @return mixed
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * @param mixed $cv
     */
    public function setCv($cv): void
    {
        $this->cv = $cv;
    }

    /**
     * @return mixed
     */
    public function getSendsms()
    {
        return $this->sendsms ?? false;
    }

    /**
     * @param mixed $sendsms
     */
    public function setSendsms($sendsms): void
    {
        $this->sendsms = $sendsms;
    }

    /**
     * @return mixed
     */
    public function getNolevel()
    {
        return $this->nolevel ?? false;
    }

    /**
     * @param mixed $nolevel
     */
    public function setNolevel($nolevel): void
    {
        $this->nolevel = $nolevel;
    }

    /**
     * Add a volunteer event for the user.
     *
     * @param Event $event
     */
    public function addVolunteerEvent(Event $event)
    {
        if (!$this->volunteerEvents->contains($event)) {
            $this->volunteerEvents[] = $event;
            $event->addVolunteer($this); // Add the user to the event's volunteers
        }
    }

    /**
     * Remove a volunteer event for the user.
     *
     * @param Event $event
     */
    public function removeVolunteerEvent(Event $event)
    {
        if ($this->volunteerEvents->contains($event)) {
            $this->volunteerEvents->removeElement($event);
            $event->removeVolunteer($this); // Remove the user from the event's volunteers
        }
    }

    /**
     * Get the user's volunteer events.
     *
     * @return ArrayCollection|Event[]
     */
    public function getVolunteerEvents()
    {
        return $this->volunteerEvents;
    }

    /**
     * @param mixed $volunteerEvents
     */
    public function setVolunteerEvents($volunteerEvents): void
    {
        $this->volunteerEvents = $volunteerEvents;
    }

    /**
     * @return mixed
     */
    public function getStudentid()
    {
        return $this->studentid;
    }

    /**
     * @param mixed $studentid
     */
    public function setStudentid($studentid): void
    {
        $this->studentid = $studentid;
    }

    /**
     * @return mixed
     */
    public function getHideattending()
    {
        return $this->hideattending;
    }

    /**
     * @param mixed $hideattending
     */
    public function setHideattending($hideattending): void
    {
        $this->hideattending = $hideattending;
    }

    /**
     * @return mixed
     */
    public function getHideleaderboards()
    {
        return $this->hideleaderboards;
    }

    /**
     * @param mixed $hideleaderboards
     */
    public function setHideleaderboards($hideleaderboards): void
    {
        $this->hideleaderboards = $hideleaderboards;
    }

    /**
     * @return mixed
     */
    public function getAthleticbookings()
    {
        return $this->athleticbookings;
    }

    /**
     * @param mixed $athleticbookings
     */
    public function setAthleticbookings($athleticbookings): void
    {
        $this->athleticbookings = $athleticbookings;
    }

    /**
     * @return mixed
     */
    public function getGetcreatorreminder()
    {
        return $this->getcreatorreminder ?? false;
    }

    /**
     * @param mixed $getcreatorreminder
     */
    public function setGetcreatorreminder($getcreatorreminder): void
    {
        $this->getcreatorreminder = $getcreatorreminder;
    }

    /**
     * @return mixed
     */
    public function getGetdailyreminders()
    {
        return $this->getdailyreminders ?? false;
    }

    /**
     * @param mixed $getdailyreminders
     */
    public function setGetdailyreminders($getdailyreminders): void
    {
        $this->getdailyreminders = $getdailyreminders;
    }

    public function getLogoPath()
    {
        return 'uploads/organizers/' . $this->logoName;
    }

    public function getLogoPlaceholder($size = "default")
    {
        if ($size == "small") {
            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAABGCAMAAABG8BK2AAAA4VBMVEUAAAD2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhFAEufoAAAASnRSTlMAAQIDBAUGBwkNDg8QERMUGx0fICUmKi04PT9BQ0ZHSUpMTVZXWVtiZGZndHV5e3+FiImMkZSanaOlsLK3ytPa3N7i6On19/n7/X4N0FUAAAFVSURBVFjD7ddpT8IwAAbglolDRQ4PwGMqHrTWA9AhTgERdWj//w9yCyauYe16LCTK3o99mydp2i4dAFmy/Mfkz4LUYopaWORlmSIN0o4p2mFRXFpm8wThTjh7iOczDIsORs5agnJD5XIpVK4kFbGzIa1QKljXsQLj8BmkwCA+gxUYrMb4g4eBb8qM6jAYh/WREdPL/TS5ngHzGOk8beYj+mlZ9XUZ9qQSXabClBVdpsCUBV3GZkpblykxZUmXaTJlU5d5gZEOjrWPH+Hvt9Jl+F3WKTVgaHe2W3aXGjGUei2n5cWMKzK8LD3zRBrV8nq52iDPuszn3Y4V6azd+y91ZnxgzdUrh6+KzDmMnQAvVJjpFnfK9lSe2eNPAfvSzBsQ5V2W6QuZvizjChl3wczEFWWyqDuV0msrpbdfSi9RcC2tECDKbSrK7J8hKego6Z8hS5Y/mG9fd7+lNzcPFwAAAABJRU5ErkJggg==";
        }

        return "data:image/png;base64,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";
    }

    /**
     */
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
     * @param File|null $logoFile
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

    /**
     * @return mixed
     */
    public function getLogoName()
    {
        return $this->logoName;
    }

    /**
     * @param mixed $logoName
     */
    public function setLogoName($logoName): void
    {
        $this->logoName = $logoName;
    }

    /**
     * @return int
     */
    public function getLogoSize(): int
    {
        return $this->logoSize;
    }

    /**
     * @param int|null $logoSize
     */
    public function setLogoSize(?int $logoSize): void
    {
        $this->logoSize = $logoSize;
    }

    /**
     * @return mixed
     */
    public function getLogoMimeType()
    {
        return $this->logoMimeType;
    }

    /**
     * @param mixed $logoMimeType
     */
    public function setLogoMimeType($logoMimeType): void
    {
        $this->logoMimeType = $logoMimeType;
    }

    /**
     * @return mixed
     */
    public function getLogoOriginalName()
    {
        return $this->logoOriginalName;
    }

    /**
     * @param mixed $logoOriginalName
     */
    public function setLogoOriginalName($logoOriginalName): void
    {
        $this->logoOriginalName = $logoOriginalName;
    }

    /**
     * @return mixed
     */
    public function getLogoDimensions()
    {
        return $this->logoDimensions;
    }

    /**
     * @param mixed $logoDimensions
     */
    public function setLogoDimensions($logoDimensions): void
    {
        $this->logoDimensions = $logoDimensions;
    }

    /**
     * @return File
     */
    public function getCoverFile()
    {
        return $this->coverFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $coverFile
     */
    public function setCoverFile(File $coverFile = null)
    {
        $this->coverFile = $coverFile;

        if (null !== $coverFile) {
// It is required that at least one field changes if you are using doctrine
// otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getCoverPath()
    {
        return 'uploads/organizers/covers/' . $this->coverName;
    }

    /**
     * @return mixed
     */
    public function getCoverName()
    {
        return $this->coverName;
    }

    /**
     * @param mixed $coverName
     */
    public function setCoverName($coverName): void
    {
        $this->coverName = $coverName;
    }

    /**
     * @return int
     */
    public function getCoverSize(): int
    {
        return $this->coverSize;
    }

    public function setCoverSize($coverSize)
    {
        $this->coverSize = $coverSize;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoverMimeType()
    {
        return $this->coverMimeType;
    }

    /**
     * @param mixed $coverMimeType
     */
    public function setCoverMimeType($coverMimeType): void
    {
        $this->coverMimeType = $coverMimeType;
    }

    /**
     * @return mixed
     */
    public function getCoverOriginalName()
    {
        return $this->coverOriginalName;
    }

    /**
     * @param mixed $coverOriginalName
     */
    public function setCoverOriginalName($coverOriginalName): void
    {
        $this->coverOriginalName = $coverOriginalName;
    }

    /**
     * @return mixed
     */
    public function getCoverDimensions()
    {
        return $this->coverDimensions;
    }

    /**
     * @param mixed $coverDimensions
     */
    public function setCoverDimensions($coverDimensions): void
    {
        $this->coverDimensions = $coverDimensions;
    }

    /**
     * @return mixed
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param mixed $bio
     */
    public function setBio($bio): void
    {
        $this->bio = $bio;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website): void
    {
        $this->website = $website;
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
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param mixed $facebook
     */
    public function setFacebook($facebook): void
    {
        $this->facebook = $facebook;
    }

    /**
     * @return mixed
     */
    public function getTiktok()
    {
        return $this->tiktok;
    }

    /**
     * @param mixed $tiktok
     */
    public function setTiktok($tiktok): void
    {
        $this->tiktok = $tiktok;
    }

    /**
     * @return mixed
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @param mixed $twitter
     */
    public function setTwitter($twitter): void
    {
        $this->twitter = $twitter;
    }

    /**
     * @return mixed
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * @param mixed $instagram
     */
    public function setInstagram($instagram): void
    {
        $this->instagram = $instagram;
    }

    /**
     * @return mixed
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * @param mixed $linkedin
     */
    public function setLinkedin($linkedin): void
    {
        $this->linkedin = $linkedin;
    }

    /**
     * @return string
     */
    public function getYoutubeurl(): string
    {
        return $this->youtubeurl ?? "";
    }

    /**
     * @param string $youtubeurl
     */
    public function setYoutubeurl(string $youtubeurl): void
    {
        $this->youtubeurl = $youtubeurl;
    }

    /**
     * @return bool
     */
    public function isShowfavs(): bool
    {
        return $this->showfavs ?? false;
    }

    /**
     * @param bool $showfavs
     */
    public function setShowfavs(bool $showfavs): void
    {
        $this->showfavs = $showfavs;
    }

    /**
     * @return bool
     */
    public function isShowattendeed(): bool
    {
        return $this->showattendeed ?? false;
    }

    /**
     * @param bool $showattendeed
     */
    public function setShowattendeed(bool $showattendeed): void
    {
        $this->showattendeed = $showattendeed;
    }

    /**
     * @return bool
     */
    public function isShowcomments(): bool
    {
        return $this->showcomments ?? false;
    }

    /**
     * @param bool $showcomments
     */
    public function setShowcomments(bool $showcomments): void
    {
        $this->showcomments = $showcomments;
    }

    /**
     * @return mixed
     */
    public function getAttendedevents()
    {
        return $this->attendedevents;
    }

    /**
     * @param mixed $attendedevents
     */
    public function setAttendedevents($attendedevents): void
    {
        $this->attendedevents = $attendedevents;
    }

    /**
     * @return mixed
     */
    public function getAttendedeventscount()
    {
        return $this->attendedeventscount;
    }

    /**
     * @param mixed $attendedeventscount
     */
    public function setAttendedeventscount($attendedeventscount): void
    {
        $this->attendedeventscount = $attendedeventscount;
    }

    /**
     * @return bool
     */
    public function isIsbusinessweek(): bool
    {
        return $this->isbusinessweek;
    }

    /**
     * @param bool $isbusinessweek
     */
    public function setIsbusinessweek(bool $isbusinessweek): void
    {
        $this->isbusinessweek = $isbusinessweek;
    }

    public function hasBoughtATicketForEvent($event)
    {
        foreach ($this->orders as $order) {
            if ($order->getStatus() == 1 && $order->containsEvent($event)) {
                return $order;
            }
        }
        return false;
    }

    public function getOrdersQuantitySum($status = 1, $upcomingtickets = "all")
    {
        $sum = 0;
        foreach ($this->orders as $order) {
            foreach ($order->getOrderelements() as $orderelement) {
                if ($status === "all" || ($orderelement->getOrder()->getStatus() === $status && ($upcomingtickets === "all" || ($upcomingtickets == 1 && $orderelement->getEventticket()->getEventdate()->getStartdate() >= new DateTime) || ($upcomingtickets == 0 && $orderelement->getEventticket()->getEventdate()->getStartdate() < new DateTime)))) {
                    $sum += $orderelement->getquantity();
                }
            }
        }
        return $sum;
    }

    public function stringifyAddress()
    {
        $address = "";
        if ($this->street) {
            $address .= $this->street . " ";
        }
        if ($this->street2) {
            $address .= $this->street2 . " ";
        }
        if ($this->city) {
            $address .= $this->city . " ";
        }
        if ($this->postalcode) {
            $address .= $this->postalcode . " ";
        }
        if ($this->state) {
            $address .= $this->state . " ";
        }
        return $address;
    }

    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $avatarFile
     */
    public function setAvatarFile(File $avatarFile = null)
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
// It is required that at least one field changes if you are using doctrine
// otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getAvatarPath()
    {
        return 'uploads/users/avatars/' . $this->avatarName;
    }

    public function getAvatarPlaceholder($size = "default")
    {
        if ($size == "small") {
            return "data:image/png;base64,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";
        }

        return "data:image/png;base64,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d/PW97fv2PX5V4eafvj5/OXbPbTYVEhk2ilRGgWJzFlKlIZAIvMVJUrlkMgspEQo5UEiM4wSoR8g0UmkKdFZCInQfkp0SiARGkOJzBlIlLzblKiMgkRqJiUirZBoeR2UaIyERGw0JRLfQCL3LSUCXQWQyBXep5g3DpIBQyjGrYNkxCKKYUchGbKdYtQpH5Ipn1MMOpOAZIx3gGLMmQQkkz6iGHLQh2TWCooR2yAZNzVFCS09E5IFNe2UkK7XQLIi+R0llK8TkGyZ1UcJ7MF4SBb1O0MJ6EA+JLvm9VICuDUaknWFByiPKrXMh9jgqWuUR3KgBGIJb9YDykM7WQexSGJFivJQzjdCLFOwKUX5Sy0jIBYq2JCi/KmTQyCWyl/ZTflDX9VBLObPukn5Pd2riyC2G3OG8p8uz/AhLqjZ3kf5X6ld9RBnJGdeofxfv7yUhLhl4PYeyv31NRAH+c+dYE5Lff6EB3FV8dxLzFHpQ+MTELeVv9nG3HNofBISB9VLfmUO6d07LgmJj9JZPzIn3N40xIPETd643Z2MtxNzKyFxVbPwJ8ZU27pGHxJvySdXX2DMtH8wrgiSG5JPrDrPmLi2ZUwhJLckHpt7oJNO6216a1geJEeVjl33U4ouOr91UgUk55WNWXWil85In944ocqDyP9TNOTVHefTtNuVPQuGl0Dkd3iljbO2nHhA6/Sd2Tl/ZIUHkb+QrHpm3ramW7RA9+lP3p5Qlw+RR+IV1T+3cMvB1l5mXsePny5/obE8AZFQEiUDn31pyfsHWu4yUun25i82Lpz8RHWBBxHTkiXVDc8+P3/Vh1+fuNDew7D6bl88+fWHK+dNHfl4VVECIpnj5xVX1D427Om/TX557pvL127atuPTvfsPNx0/+WPzqdMtZ8+fP9dy+ueffjx5/PvD+7/4dMfWjauXvTH7hQmjnmyoqyrJT3gQERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERyT3/B8MMTwPSkGz9AAAAAElFTkSuQmCC";
    }

    public function isAdmin()
    {
        if ($this->hasRole("ROLE_ADMINISTRATOR")) {
            return true;
        }

        return false;
    }

    public function hasRole($role)
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function getCrossRoleName()
    {
        if ($this->hasRole("ROLE_ATTENDEE")) {
            return $this->getFullName();
        }

        if ($this->hasRole("ROLE_ORGANIZER") && $this->organizer) {
            return $this->organizer->getName();
        }

        return "Administrator";
    }

    public function getFullName()
    {
        if ($this->gender) {
            return $this->gender . " " . $this->firstname . " " . $this->lastname;
        }

        return $this->firstname . " " . $this->lastname;
    }

    public function hasSocialMedia()
    {
        return ($this->website || $this->email || $this->phone || $this->twitter || $this->instagram || $this->facebook || $this->linkedin);
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

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
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

    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function addOrder($order)
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder($order)
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
// set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    public function addFavorite($favorite)
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->addAddedtofavoritesby($this);
        }

        return $this;
    }

    public function removeFavorite($favorite)
    {
        if ($this->favorites->contains($favorite)) {
            $this->favorites->removeElement($favorite);
            $favorite->removeAddedtofavoritesby($this);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFollowing()
    {
        return $this->following;
    }

    public function addFollowing($following)
    {
        if (!$this->following->contains($following)) {
            $this->following[] = $following;
            $following->addFollowedby($this);
        }

        return $this;
    }

    public function addFollowedby($followedby)
    {
        if (!$this->followedby->contains($followedby)) {
            $this->followedby[] = $followedby;
        }

        return $this;
    }

    public function removeFollowing($following)
    {
        if ($this->following->contains($following)) {
            $this->following->removeElement($following);
            $following->removeFollowedby($this);
        }

        return $this;
    }

    public function removeFollowedby($followedby)
    {
        if ($this->followedby->contains($followedby)) {
            $this->followedby->removeElement($followedby);
        }

        return $this;
    }

    public function getIsorganizeronhomepageslider()
    {
        return $this->isorganizeronhomepageslider;
    }

    public function setIsorganizeronhomepageslider($isorganizeronhomepageslider)
    {
        $this->isorganizeronhomepageslider = $isorganizeronhomepageslider;

        return $this;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    public function getStreet2()
    {
        return $this->street2;
    }

    public function setStreet2($street2)
    {
        $this->street2 = $street2;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    public function getPostalcode()
    {
        return $this->postalcode;
    }

    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTicketreservations()
    {
        return $this->ticketreservations;
    }

    public function addTicketreservation($ticketreservation)
    {
        if (!$this->ticketreservations->contains($ticketreservation)) {
            $this->ticketreservations[] = $ticketreservation;
            $ticketreservation->setUser($this);
        }

        return $this;
    }

    public function removeTicketreservation($ticketreservation)
    {
        if ($this->ticketreservations->contains($ticketreservation)) {
            $this->ticketreservations->removeElement($ticketreservation);
// set the owning side to null (unless already changed)
            if ($ticketreservation->getUser() === $this) {
                $ticketreservation->setUser(null);
            }
        }

        return $this;
    }

    public function getAvatarName()
    {
        return $this->avatarName;
    }

    public function setAvatarName($avatarName)
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    public function getAvatarSize()
    {
        return $this->avatarSize;
    }

    public function setAvatarSize($avatarSize)
    {
        $this->avatarSize = $avatarSize;

        return $this;
    }

    public function getAvatarMimeType()
    {
        return $this->avatarMimeType;
    }

    public function setAvatarMimeType($avatarMimeType)
    {
        $this->avatarMimeType = $avatarMimeType;

        return $this;
    }

    public function getAvatarOriginalName()
    {
        return $this->avatarOriginalName;
    }

    public function setAvatarOriginalName($avatarOriginalName)
    {
        $this->avatarOriginalName = $avatarOriginalName;

        return $this;
    }

    public function getAvatarDimensions()
    {
        return $this->avatarDimensions;
    }

    public function setAvatarDimensions($avatarDimensions)
    {
        $this->avatarDimensions = $avatarDimensions;

        return $this;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function stringifypointslevel()
    {
        if ($this->getRole() == "Organizer") {
            if ($this->getOrganizer()->getCategory() == "Club" || $this->getOrganizer()->getCategory() == "Society") {
                $points = $this->getPoints();
                if ($points >= 40) {
                    return "Master of the Universe";
                }

                if ($points >= 30) {
                    return "Master of Most";
                }

                if ($points >= 20) {
                    return "Master of One";
                }

                if ($points >= 10) {
                    return "Learner of Many Things";
                }

                if ($points >= 0) {
                    return "Learner of One";
                }
            } else {
                return "";
            }
        } elseif ($this->getRole() == "Attendee") {
            $points = $this->getPoints();

            if ($points >= 1 && $points < 2) {
                return "- Smurfantastic";
            }

            if ($points >= 2 && $points < 3) {
                return "- Wubba Lubba Dub Dub!";
            }

            if ($points >= 3 && $points < 4) {
                return "- The Force Awakens";
            }

            if ($points >= 4) {
                return "- Intergalactic";
            }
        }

        return "";
    }

    public function getRole()
    {
        if ($this->hasRole("ROLE_ATTENDEE")) {
            return "Attendee";
        }

        if ($this->hasRole("ROLE_ORGANIZER")) {
            return "Organizer";
        }

        if ($this->hasRole("ROLE_SUPER_ADMIN") || $this->hasRole("ROLE_ADMINISTRATOR")) {
            return "Administrator";
        }

        return "N/A";
    }

    public function getOrganizer()
    {
        return $this->organizer;
    }

    public function setOrganizer($organizer)
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param mixed $points
     */
    public function setPoints($points): void
    {
        $this->points = $points;
    }

    public function isAdministratorUser()
    {
        if ($this->getId() == 109) {
            return true;
        }

        return false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isClubOrSociety()
    {
        $points = $this->getPoints();
        return ($this->getRole() == "Organizer") && ($this->getOrganizer()->getCategory() == "Club" || $this->getOrganizer()->getCategory() == "Society");
    }

    public function orggoodrole()
    {
        $points = $this->getPoints();
        return ($this->getRole() == "Organizer") && ($this->getOrganizer()->getCategory() == "Club" || $this->getOrganizer()->getCategory() == "Society") && $points >= 10;
    }

    public function willshowup()
    {
        if ($this->getRole() == "Organizer") {
            if ($this->getOrganizer()->getCategory() !== "Club" || $this->getOrganizer()->getCategory() !== "Society") {
                return false;
            }

//  check points
        } else if ($this->getRole() == "Attendee") {
            //  check points
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getSelectedImage()
    {
        return $this->selectedImage;
    }

    /**
     * @param mixed $selectedImage
     */
    public function setSelectedImage($selectedImage): void
    {
        $this->selectedImage = $selectedImage;
    }


    public function imagiifypointslevel()
    {
        if ($this->getRole() == "Organizer") {
            if ($this->getOrganizer()->getCategory() == "Club" || $this->getOrganizer()->getCategory() == "Society") {
                $points = $this->getPoints();

                if ($points >= 50) {
                    return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "LEVELS_ATTENDEES-05-05.png";
                }

                if ($points >= 40) {
                    return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "LEVELS_ATTENDEES-04.png";
                }

                if ($points >= 30) {
                    return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "LEVELS_ATTENDEES-03.png";
                }

                if ($points >= 20) {
                    return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "LEVELS_ATTENDEES-02.png";
                }

//                if ($points >= 10) {
                return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "LEVELS_ATTENDEES-01.png";
//                }

                return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "LEVELS_ATTENDEES-01.png";
            } else {
                return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "LEVELS_ATTENDEES-01.png";
            }
        } elseif ($this->getRole() == "Attendee") {
            if ($this->getSpecialuser()) {
                return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "special.png";
            }
            $points = $this->getPoints();

            if ($points >= 1 && $points < 2) {
                return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "smurf.png";
            }

            if ($points >= 2 && $points < 3) {
                return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "ricky.png";
            }

            if ($points >= 3 && $points < 4) {
                return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "force.png";
            }

            if ($points >= 4) {
                return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "inter.png";
            }

        }

        return 'https://universe.alexandroskourtis.me/uploads/imgs/' . "smurf.png";
    }

    /**
     * @return mixed
     */
    public function getSpecialuser()
    {
        return $this->specialuser;
    }

    /**
     * @param mixed $specialuser
     */
    public function setSpecialuser($specialuser): void
    {
        $this->specialuser = $specialuser;
    }

    public function stringifypointslevelAttendee()
    {
        $points = $this->getPoints();

        if ($points >= 1 && $points < 2) {
            return "Smurfantastic";
        }

        if ($points >= 2 && $points < 3) {
            return "Wubba Lubba Dub Dub!";
        }

        if ($points >= 3 && $points < 4) {
            return "The Force Awakens";
        }

        if ($points >= 4) {
            return "Intergalactic";
        }

        return "";
    }
}
