<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use App\Validator\Constraints\EnglishOnly;
use App\Validator\Constraints\Profanity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'universe_order')]
#[ORM\Index(name: 'idx_order_status', columns: ['status'])]
#[ORM\Entity(repositoryClass: \App\Repository\OrderRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $overbook;
    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class, inversedBy: 'orders')]
    private $user;
    #[ORM\OneToMany(targetEntity: \App\Entity\OrderElement::class, cascade: ['persist', 'remove'], mappedBy: 'order', fetch: 'LAZY')]
    private $orderelements;
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $reference;
    /**
     * @var string
     * @EnglishOnly
     * @Profanity
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $note;
    /**
     * -2: failed / -1: cancel / 0: waiting for payment / 1: paid
     * @var integer
     */
    #[ORM\Column(type: 'integer')]
    private $status;
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


    public function __construct()
    {
        $this->status = 0;
        $this->orderelements = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getOverbook()
    {
        return $this->overbook;
    }

    /**
     * @param mixed $overbook
     */
    public function setOverbook($overbook): void
    {
        $this->overbook = $overbook;
    }


    public function getOverbookStringified()
    {
        if ($this->overbook) {
            return "True";
        }
        return "False";
    }

    public function containsEvent($event)
    {
        foreach ($this->orderelements as $orderElement) {
            if ($orderElement->getEventticket()->getEventdate()->getEvent() == $event) {
                return true;
            }
        }
        return false;
    }

// -2: failed / -1: cancel / 0: waiting for payment / 1: paid

    public function getStatusClass()
    {

        switch ($this->status) {
            case -1:
            case -2:
                return "danger";
                break;
            case 0:
                return "warning";
                break;
            case 1:
                return "success";
                break;
            default:
                return "danger";
        }
    }

    public function stringifyStatus()
    {

        switch ($this->status) {
            case -1:
                return "Canceled";
                break;
            case 1:
                return "Booked (Not Attended)";
            case 2:
                return "Attendeed";
                break;
            default:
                return "Unknown";
        }
    }

    public function getOrderElementsQuantitySum($status = "all", $organizer = "all")
    {
        $count = 0;
        if ($status == "all" || $this->status === $status) {
            foreach ($this->orderelements as $orderelement) {
                if ($organizer == "all" || $orderelement->getEventticket()->getEventdate()->getEvent()->getOrganizer()->getSlug() == $organizer) {
                    $count += $orderelement->getQuantity();
                }
            }
        }
        return $count;
    }

    public function getNotFreeOrderElementsQuantitySum()
    {
        $sum = 0;
        foreach ($this->orderelements as $orderelement) {
            if (!$orderelement->getEventticket()->getFree()) {
                $sum += $orderelement->getQuantity();
            }
        }
        return $sum;
    }

    public function getId()
    {
        return $this->id;
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

    public function getStatusStringified()
    {
        $localstatus = $this->getStatus();

        if ($localstatus == 1) {
            return "Not Attendeed";
        }

        if ($localstatus == 2) {
            return "Attendeed";
        }

// Handle other status values if needed
        return "unknown"; // or any other appropriate handling
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderelements()
    {
        return $this->orderelements;
    }

    public function addOrderelement($orderelement)
    {
        if (!$this->orderelements->contains($orderelement)) {
            $this->orderelements[] = $orderelement;
            $orderelement->setOrder($this);
        }

        return $this;
    }

    public function removeOrderelement($orderelement)
    {
        if ($this->orderelements->contains($orderelement)) {
            $this->orderelements->removeElement($orderelement);
// set the owning side to null (unless already changed)
            if ($orderelement->getOrder() === $this) {
                $orderelement->setOrder(null);
            }
        }

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

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

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

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }
}
