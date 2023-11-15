<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="operating_hours")
 */
class OperatingHours
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $dayOfWeek;

    /**
     * @ORM\ManyToOne(targetEntity="MonthlySchedule", inversedBy="operatingHours")
     */
    private $monthlySchedule;

    /**
     * @ORM\Column(type="time")
     */
    private $startTime;
    /**
     * @ORM\Column(type="time")
     */
    private $endTime;

    /**
     * @return mixed
     */
    public function getMonthlySchedule()
    {
        return $this->monthlySchedule;
    }

    /**
     * @param mixed $monthlySchedule
     */
    public function setMonthlySchedule($monthlySchedule): void
    {
        $this->monthlySchedule = $monthlySchedule;
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
    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    /**
     * @param mixed $dayOfWeek
     */
    public function setDayOfWeek($dayOfWeek): void
    {
        $this->dayOfWeek = $dayOfWeek;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime): void
    {
        $this->endTime = $endTime;
    }

    // Getters and setters

}