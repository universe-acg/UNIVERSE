<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'universe_managementclubssocieties')]
#[ORM\Entity(repositoryClass: \App\Repository\ManagementClubsSocietiesRepository::class)]
class ManagementClubsSocieties
{
    private $isinitiatedbyme;
    /**
     * @var DateTime $createdAt
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private $createdAt;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\JoinColumn(name: 'userid', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class, fetch: 'LAZY')]
    private $user;
    #[ORM\JoinColumn(name: 'orgid', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Organizer::class, fetch: 'LAZY')]
    private $org;
    #[ORM\Column(type: 'string', length: 255)]
    private $status;
    #[ORM\JoinColumn(name: 'initiatedby', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    private $initiatedBy;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getIsinitiatedbyme()
    {
        return $this->isinitiatedbyme;
    }

    /**
     * @param mixed $isinitiatedbyme
     */
    public function setIsinitiatedbyme($isinitiatedbyme): void
    {
        $this->isinitiatedbyme = $isinitiatedbyme;
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

    public function stringifystatus(): ?string
    {
        if ($this->getStatus() == -1) {
            return "Not Approved";
        }

        if ($this->getStatus() == 0) {
            return "Pending";
        }

        if ($this->getStatus() == 1) {
            return "Approved - Member";
        }

        return null;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
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
    public function getOrg()
    {
        return $this->org;
    }

    /**
     * @param mixed $org
     */
    public function setOrg($org): void
    {
        $this->org = $org;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitiatedBy(): ?User
    {
        return $this->initiatedBy;
    }

    public function setInitiatedBy(User $user): self
    {
        $this->initiatedBy = $user;

        return $this;
    }
}
