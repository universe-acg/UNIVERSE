<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'universe_athletic_booking')]
#[ORM\Entity(repositoryClass: \App\Repository\AthleticBookingRepository::class)]
#[Assert\Callback(['App\Validation\Validator', 'validate'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
#[Vich\Uploadable]
class AthleticBooking
{


    #[ORM\Column(type: 'boolean')]
    private $status;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $amount;
    #[ORM\JoinColumn(name: 'facility_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\FacilityEntity::class)]
    private $facility;
    #[ORM\Column(type: 'integer')]
    private $duration;
    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class, inversedBy: 'athleticbookings')]
    private $user;

    private $ispast;
    #[ORM\Column(type: 'datetime', nullable: false)]
    private $bookingDate;

    private $bookingTime;

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

    /**
     * @return mixed
     */
    public function getIspast()
    {
        return $this->ispast;
    }

    /**
     * @param mixed $ispast
     */
    public function setIspast($ispast): void
    {
        $this->ispast = $ispast;
    }

    /**
     * @return mixed
     */
    public function getBookingTime()
    {
        return $this->bookingTime;
    }

    /**
     * @param mixed $bookingTime
     */
    public function setBookingTime($bookingTime): void
    {
        $this->bookingTime = $bookingTime;
    }

    public function stringifyStatus()
    {
        if ($this->status == 0) {
            return "Reserved";
        }

        return "Looking for people to join...";
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
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
    public function getFacility()
    {
        return $this->facility;
    }

    /**
     * @param mixed $facility
     */
    public function setFacility($facility): void
    {
        $this->facility = $facility;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getBookingDate()
    {
        return $this->bookingDate;
    }

    /**
     * @param mixed $bookingDate
     */
    public function setBookingDate($bookingDate): void
    {
        $this->bookingDate = $bookingDate;
    }
}
