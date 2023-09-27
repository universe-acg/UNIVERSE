<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Datetime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventTicketRepository")
 * @ORM\Table(name="universe_event_date_ticket")
 */
class EventTicket
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="EventDate", inversedBy="tickets")
     */
    private $eventdate;

    /**
     * @ORM\OneToMany(targetEntity="OrderElement", mappedBy="eventticket", cascade={"remove"})
     */
    private $orderelements;

    /**
     * @ORM\OneToMany(targetEntity="TicketReservation", mappedBy="eventticket", cascade={"remove"})
     */
    private $ticketreservations;

//         * @Assert\NotNull(groups={"create", "update"})
    /**
     * @ORM\Column(type="boolean")
     */
    private $active = 1;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"create", "update"})
     * @Assert\Length(min = 2, groups={"create", "update"})
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(groups={"create", "update"})
     */
    private $quantity;

//    * @Assert\LessThan(propertyPath="quantity", groups={"create", "update"})
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ticketsperattendee = 1;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $salesstartdate;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\GreaterThan(propertyPath="salesstartdate", groups={"create", "update"})
     */
    private $salesenddate;

    /**
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->orderelements = new ArrayCollection();
        $this->reference = $this->generateReference(10);
        $this->cartelements = new ArrayCollection();
        $this->ticketreservations = new ArrayCollection();
        $this->salesstartdate = new DateTime();
        $this->salesenddate = new DateTime();
        $this->name = "Ticket";
        $this->description = "";
        $this->price = 0;
        $this->free = 1;
    }

    /**
     * @throws Exception
     */
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

    public function getId()
    {
        return $this->id;
    }

    public function getTotalCheckInPercentage(): float
    {
        if ($this->getOrderElementsQuantitySum() == 0) {
            return 0;
        }
        return round(($this->getScannedTicketsCount() / $this->getOrderElementsQuantitySum()) * 100);
    }

    public function getOrderElementsQuantitySum($status = 1, $user = "all", $role = "all")
    {
        $sum = 0;
        foreach ($this->orderelements as $orderelement) {
            if (($status == "all" || $orderelement->getOrder()->getStatus() === $status) && ($user == "all" || $orderelement->getOrder()->getUser() == $user) && ($role == "all" || $orderelement->getOrder()->getUser()->hasRole($role))) {
                $sum += $orderelement->getQuantity();
            }
        }
        return $sum;
    }

    public function getQuantity(): int
    {
        if ($this->quantity == null) {
            return 0;
        }
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getScannedTicketsCount(): int
    {
        $count = 0;
        foreach ($this->orderelements as $orderElement) {
            foreach ($orderElement->getTickets() as $ticket) {
                if ($ticket->getScanned()) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getSalesPercentage(): float
    {
        if ($this->quantity == 0) {
            return 0;
        }

        return round(($this->getOrderElementsQuantitySum() / $this->quantity) * 100);
    }

    public function getSales($role = "all", $user = "all", $formattedForPayoutApproval = false, $includeFees = false)
    {
        $sum = 0;
        foreach ($this->orderelements as $orderelement) {
            if ($orderelement->getOrder()->getStatus() === 1 && ($role == "all" || $orderelement->getOrder()->getUser()->hasRole($role)) && ($user == "all" || $orderelement->getOrder()->getUser() == $user)) {
                $sum += $orderelement->getPrice($formattedForPayoutApproval);
            }
        }
        if ($includeFees) {
            $sum += $this->getTotalFees();
        }
        return $sum;
    }

    public function getPrice(): float
    {
        return (float)$this->price;
    }

    public function getTotalFees(): float
    {
        $sum = 0;
        $sum += $this->getTotalTicketFees();
        return $sum;
    }

    public function getTotalTicketFees($role = "all", $user = "all"): float
    {
        $sum = 0;
        foreach ($this->orderelements as $orderelement) {
            if ($orderelement->getOrder()->getStatus() === 1 && ($role == "all" || $orderelement->getOrder()->getUser()->hasRole($role)) && ($user == "all" || $orderelement->getOrder()->getUser() == $user) && !$this->free) {
                $sum += $orderelement->getOrder()->getTicketFee() * $orderelement->getQuantity();
            }
        }
        return $sum;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getTicketPricePercentageCutSum($role = "all"): float
    {
        $sum = 0;
        foreach ($this->orderelements as $orderelement) {
            if ($role == "all" || $orderelement->getOrder()->getUser()->hasRole($role)) {
                $sum += (($orderelement->getOrder()->getTicketPricePercentageCut() * $orderelement->getUnitprice()) / 100) * $orderelement->getQuantity();
            }
        }
        return $sum;
    }

    public function isOnSale(): bool
    {
        if (!$this->eventdate || !$this->eventdate->getEvent() || !$this->eventdate->getEvent()->getOrganizer() || !$this->eventdate->getEvent()->getOrganizer()->getUser()) {
            return false;
        }

        return
            $this->eventdate->getEvent()->getOrganizer()->getUser()->isEnabled() && $this->eventdate->getEvent()->getPublished() && $this->eventdate->getActive() && ($this->eventdate->getStartdate() >= new Datetime) && $this->active && !$this->isSoldOut() && ($this->salesstartdate < new Datetime || !$this->salesstartdate) && ($this->salesenddate > new Datetime || !$this->salesenddate);
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

    public function isSoldOut(): bool
    {
        return !($this->quantity == 0 || $this->getTicketsLeftCount() > 0);
    }

    public function getTicketsLeftCount($countVlidTicketReservations = true)
    {
        if ($countVlidTicketReservations) {
            return $this->quantity - $this->getOrderElementsQuantitySum() - $this->getValidTicketReservationsQuantitySum();
        }

        return $this->quantity - $this->getOrderElementsQuantitySum();
    }

    public function getValidTicketReservationsQuantitySum($user = null)
    {
        $sum = 0;
        foreach ($this->ticketreservations as $ticketreservation) {
            if (!$ticketreservation->isExpired() && ($user == null || $ticketreservation->getUser() == $user)) {
                $sum += $ticketreservation->getQuantity();
            }
        }
        return $sum;
    }

    public function stringifyStatus(): string
    {
        if (!$this->eventdate->getEvent()->getOrganizer()->getUser()->isEnabled()) {
            return "Organizer is disabled";
        }

        if (!$this->eventdate->getEvent()->getPublished()) {
            return "Event is not published";
        }

        if (!$this->eventdate->getActive()) {
            return "Event date is disabled";
        }

        $currentDateTime = new DateTime();

        if ($currentDateTime < $this->eventdate->getStartdate()) {
            return "Event has not started yet";
        }

        if ($currentDateTime >= $this->eventdate->getStartdate() && $currentDateTime <= $this->eventdate->getEnddate()) {
            return "Event has started";
        }

        if ($currentDateTime > $this->eventdate->getEnddate()) {
            return "Event has ended";
        }

        if (!$this->active) {
            return "Event ticket is disabled";
        }

        if ($this->isSoldOut()) {
            return "Sold out";
        }

        if ($this->salesstartdate > new Datetime && $this->salesstartdate) {
            return "Tickets haven't started yet";
        }

        if ($this->salesenddate < new Datetime && $this->salesstartdate) {
            return "Tickets ended";
        }

        return "Tickets active";
    }

    public function stringifyStatusClass(): string
    {
        if (!$this->eventdate->getEvent()->getOrganizer()->getUser()->isEnabled()) {
            return "danger";
        }

        if (!$this->eventdate->getEvent()->getPublished()) {
            return "danger";
        }

        if (!$this->eventdate->getActive()) {
            return "danger";
        }

        if ($this->eventdate->getStartdate() < new Datetime) {
            return "info";
        }

        if (!$this->active) {
            return "danger";
        }

        if ($this->isSoldOut()) {
            return "warning";
        }

        if ($this->salesstartdate > new Datetime && $this->salesstartdate) {
            return "info";
        }

        if ($this->salesenddate < new Datetime && $this->salesstartdate) {
            return "warning";
        }

        return "success";
    }

    public function getPromotionalprice(): float
    {
        return (float)$this->promotionalprice;
    }

    public function setPromotionalprice($promotionalprice)
    {
        $this->promotionalprice = $promotionalprice;

        return $this;
    }

    public function getEventdate()
    {
        return $this->eventdate;
    }

    public function setEventdate($eventdate)
    {
        $this->eventdate = $eventdate;

        return $this;
    }

    public function getName(): string
    {
        if ($this->name == null) {
            return "";
        }

        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getTicketsperattendee(): int
    {
        return $this->ticketsperattendee;
    }

    public function setTicketsperattendee($ticketsperattendee)
    {
        $this->ticketsperattendee = $ticketsperattendee;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderelements(): ArrayCollection
    {
        return $this->orderelements;
    }

    public function addOrderelement($orderelement)
    {
        if (!$this->orderelements->contains($orderelement)) {
            $this->orderelements[] = $orderelement;
            $orderelement->setEventticket($this);
        }

        return $this;
    }

    public function removeOrderelement($orderelement)
    {
        if ($this->orderelements->contains($orderelement)) {
            $this->orderelements->removeElement($orderelement);
// set the owning side to null (unless already changed)
            if ($orderelement->getEventticket() === $this) {
                $orderelement->setEventticket(null);
            }
        }

        return $this;
    }

    public function getSalesstartdate(): Datetime
    {
        return $this->salesstartdate;
    }

    public function setSalesstartdate($salesstartdate)
    {
        $this->salesstartdate = $salesstartdate;

        return $this;
    }

    public function getSalesenddate(): Datetime
    {
        return $this->salesenddate;
    }

    public function setSalesenddate($salesenddate)
    {
        $this->salesenddate = $salesenddate;

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

    /**
     * @return Collection
     */
    public function getCartelements(): Collection
    {
        return $this->cartelements;
    }

    public function addCartelement($cartelement)
    {
        if (!$this->cartelements->contains($cartelement)) {
            $this->cartelements[] = $cartelement;
            $cartelement->setEventticket($this);
        }

        return $this;
    }

    public function removeCartelement($cartelement)
    {
        if ($this->cartelements->contains($cartelement)) {
            $this->cartelements->removeElement($cartelement);
// set the owning side to null (unless already changed)
            if ($cartelement->getEventticket() === $this) {
                $cartelement->setEventticket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTicketreservations(): Collection
    {
        return $this->ticketreservations;
    }

    public function addTicketreservation($ticketreservation)
    {
        if (!$this->ticketreservations->contains($ticketreservation)) {
            $this->ticketreservations[] = $ticketreservation;
            $ticketreservation->setEventticket($this);
        }

        return $this;
    }

    public function removeTicketreservation($ticketreservation)
    {
        if ($this->ticketreservations->contains($ticketreservation)) {
            $this->ticketreservations->removeElement($ticketreservation);
// set the owning side to null (unless already changed)
            if ($ticketreservation->getEventticket() === $this) {
                $ticketreservation->setEventticket(null);
            }
        }

        return $this;
    }

    public function getFree()
    {
        return $this->free;
    }

    public function setFree($free)
    {
        $this->free = $free;

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
}
