<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'universe_listing_date_positions')]
#[ORM\Entity(repositoryClass: \App\Repository\ListingDatePositionRepository::class)]
#[Vich\Uploadable]
class ListingDatePosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var DateTime $createdAt
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private $createdAt;

    /**
     * @var DateTime $updatedAt
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Listing::class, inversedBy: 'datePositions')]
    private $listing;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: false)]
    private $startdate;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: false)]
    private $enddate;

    #[ORM\Column(type: 'boolean')]
    private $allowoverapply;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $shortdescription;
    /**
     *
     * @var integer
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $candidatesamount;

    public function __construct()
    {
        $this->startdate = new DateTime(); // or set it to an appropriate default value
        $this->enddate = new DateTime(); // or set it to an appropriate default value
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
    public function getListing()
    {
        return $this->listing;
    }

    /**
     * @param mixed $listing
     */
    public function setListing($listing): void
    {
        $this->listing = $listing;
    }

    /**
     * @return DateTime
     */
    public function getStartdate(): ?DateTime
    {
        return $this->startdate;
    }

    /**
     * @param DateTime $startdate
     */
    public function setStartdate(DateTime $startdate): void
    {
        $this->startdate = $startdate;
    }

    /**
     * @return DateTime
     */
    public function getEnddate(): ?DateTime
    {
        return $this->enddate;
    }

    /**
     * @param DateTime $enddate
     */
    public function setEnddate(DateTime $enddate): void
    {
        $this->enddate = $enddate;
    }

    /**
     * @return mixed
     */
    public function getAllowoverapply()
    {
        return $this->allowoverapply;
    }

    /**
     * @param mixed $allowoverapply
     */
    public function setAllowoverapply($allowoverapply): void
    {
        $this->allowoverapply = $allowoverapply;
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
     * @return int
     */
    public function getCandidatesamount(): ?int
    {
        return $this->candidatesamount;
    }

    /**
     * @param int $candidatesamount
     */
    public function setCandidatesamount(int $candidatesamount): void
    {
        $this->candidatesamount = $candidatesamount;
    }


}
