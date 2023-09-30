<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
    use Datetime;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\EventDateRepository")
     * @ORM\Table(name="universe_event_date")
     */
    class EventDate
    {
        
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;
        
        /**
         * @ORM\ManyToOne(targetEntity="Event", inversedBy="eventdates", cascade={"persist"})
         */
        private $event;
        
        /**
         * @ORM\Column(type="boolean")
         */
        private $online = false;
        
        /**
         * @ORM\OneToMany(targetEntity="EventTicket", mappedBy="eventdate", cascade={"persist", "remove"}, fetch = "EAGER", orphanRemoval=true)
         * @ORM\OrderBy({"position" = "ASC"})
         * @Assert\Valid(groups={"create", "update"})
         */
        private $tickets;
        
        /**
         * @ORM\Column(type="boolean")
         */
        private $active = true;
        
        /**
         * @var string
         * @ORM\Column(type="string", length=255)
         */
        private $reference;
        
        /**
         * @var string
         * @ORM\Column(type="string")
         */
        private $description;
        
        /**
         * @var string
         * @ORM\Column(type="string", length=255)
         */
        private $title;
        
        /**
         * @var DateTime
         * @ORM\Column(type="datetime", nullable=true)
         * @Assert\NotBlank(groups={"create", "update"})
         */
        private $startdate;
        /**
         * @var DateTime
         * @ORM\Column(type="datetime", nullable=true)
         * @Assert\GreaterThan(propertyPath="startdate", groups={"create", "update"})
         */
        private $enddate;
        
        public function __construct()
        {
            $this->tickets = new ArrayCollection();
            $this->reference = $this->generateReference(10);
            $this->startdate = new Datetime();
            $this->enddate = new Datetime();
            $this->active = true;
        }
        
        public function generateReference($length): string
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
         * @return string
         */
        public function getDescription(): string
        {
            return $this->description;
        }
        
        /**
         * @param string $description
         */
        public function setDescription(string $description): void
        {
            $this->description = $description;
        }
        
        /**
         * @return string
         */
        public function getTitle(): string
        {
            return $this->title;
        }
        
        /**
         * @param string $title
         */
        public function setTitle(string $title): void
        {
            $this->title = $title;
        }
        
        public function getId()
        {
            return $this->id;
        }
        
        public function stringifyStatus(): string
        {
            if (!$this->event->getOrganizer()->getUser()->isEnabled()) {
                return "Organizer is disabled";
            }
            
            if (!$this->event->getPublished()) {
                return "Event is not published";
            }
            
            if (!$this->active) {
                return "Event date is disabled";
            }
            
            if ($this->getStartdate() < new Datetime) {
                return "Event already started/ended";
            }
            
            if ($this->isSoldOut()) {
                return "Sold out";
            }
            
            if (!$this->hasATicketOnSale()) {
                return "No ticket on sale";
            }
            
            return "On sale";
        }
        
        public function getStartdate(): Datetime
        {
            return $this->startdate;
        }
        
        public function setStartdate($startdate)
        {
            $this->startdate = $startdate;
            
            return $this;
        }
        
        public function isSoldOut(): bool
        {
            foreach ($this->tickets as $ticket) {
                if (!$ticket->isSoldOut()) {
                    return false;
                }
            }
            return true;
        }
        
        public function hasATicketOnSale(): bool
        {
            foreach ($this->tickets as $ticket) {
                if ($ticket->isOnSale()) {
                    return true;
                }
            }
            return false;
        }
        
        public function isOnSale(): bool
        {
            return (
                $this->event->getOrganizer()->getUser()->isEnabled() && $this->event->getPublished() && ($this->getStartdate() > new Datetime) && $this->hasATicketOnSale()
            );
        }
        
        public function getScannedTicketsCount()
        {
            $count = 0;
            foreach ($this->tickets as $ticket) {
                $count += $ticket->getScannedTicketsCount();
            }
            return $count;
        }
        
        
        public function getTotalSalesPercentage(): float
        {
            if ($this->getTicketsQuantitySum() == 0) {
                return 0;
            }
            
            return round(($this->getOrderElementsQuantitySum() / $this->getTicketsQuantitySum()) * 100);
        }
        
        public function getTicketsQuantitySum()
        {
            $sum = 0;
            foreach ($this->tickets as $eventDateTicket) {
                $sum += $eventDateTicket->getQuantity();
            }
            return $sum;
        }
        
        public function getOrderElementsQuantitySum($status = 1, $user = "all", $role = "all")
        {
            $sum = 0;
            foreach ($this->tickets as $ticket) {
                $sum += $ticket->getOrderElementsQuantitySum($status, $user, $role);
            }
            return $sum;
        }
        
        public function getSales($role = "all", $user = "all")
        {
            $sum = 0;
            foreach ($this->tickets as $eventDateTicket) {
                $sum += $eventDateTicket->getSales($role, $user);
            }
            return $sum;
        }
        
        public function stringifyStatusClass(): string
        {
            if (!$this->event->getOrganizer()->getUser()->isEnabled()) {
                return "danger";
            }
            
            if (!$this->active) {
                return "danger";
            }
            
            if (!$this->event->getPublished()) {
                return "warning";
            } else if ($this->getStartdate() < new Datetime) {
                return "info";
            } else if ($this->isSoldOut()) {
                return "warning";
            } else if (!$this->hasATicketOnSale()) {
                return "danger";
            } else {
                return "success";
            }
        }
        
        public function getCheapestTicket()
        {
            $cheapestticket = $this->tickets[0];
            foreach ($this->tickets as $ticket) {
                if ($ticket->getSalePrice() > 0) {
                    if ($ticket->getSalePrice() < $cheapestticket->getSalePrice()) {
                        $cheapestticket = $ticket;
                    }
                }
            }
            return $cheapestticket;
        }
        
        
        public function getTickets()
        {
            return $this->tickets;
        }
        
        public function addTicket($ticket)
        {
            if (!$this->tickets->contains($ticket)) {
                $this->tickets[] = $ticket;
                $ticket->setEventdate($this);
            }
            
            return $this;
        }
        
        public function removeTicket($ticket)
        {
            if ($this->tickets->contains($ticket)) {
                $this->tickets->removeElement($ticket);
// set the owning side to null (unless already changed)
                if ($ticket->getEventdate() === $this) {
                    $ticket->setEventdate(null);
                }
            }
            
            return $this;
        }
        
        public function getActive()
        {
            return $this->active;
        }
        
        public function setActive($active)
        {
            $this->active = $active;
            
            return $this;
        }
        
        public function getReference(): string
        {
            if ($this->reference === null) {
                // Generate a reference if it's null
                $this->reference = $this->generateReference(10);
            }
            
            return $this->reference;
        }
        
        public function setReference($reference)
        {
            $this->reference = $reference;
            
            return $this;
        }
        
        public function getEnddate(): Datetime
        {
            return $this->enddate;
        }
        
        public function setEnddate($enddate)
        {
            $this->enddate = $enddate;
            
            return $this;
        }
        
        public function getEvent()
        {
            return $this->event;
        }
        
        public function setEvent($event)
        {
            $this->event = $event;
            
            return $this;
        }
        
        public function getOnline()
        {
            return $this->online;
        }
        
        public function setOnline($online)
        {
            $this->online = $online;
            
            return $this;
        }
    }
