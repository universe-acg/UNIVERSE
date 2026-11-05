<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'monthly_schedule')]
#[ORM\Entity]
class MonthlySchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date', nullable: false)]
    private $startDate;

    #[ORM\Column(type: 'date', nullable: false)]
    private $endDate;
    #[ORM\ManyToOne(targetEntity: \App\Entity\FacilityEntity::class, inversedBy: 'monthlySchedule', cascade: ['persist'])]
    private $facility;
    #[ORM\OneToMany(targetEntity: \App\Entity\OperatingHours::class, mappedBy: 'monthlySchedule', cascade: ['persist', 'remove'])]
    private $operatingHours;

    public function __construct()
    {
        $this->operatingHours = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getOperatingHours()
    {
        return $this->operatingHours;
    }

    /**
     * @param mixed $operatingHours
     */
    public function setOperatingHours($operatingHours): void
    {
        $this->operatingHours = $operatingHours;
    }


    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
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

}
