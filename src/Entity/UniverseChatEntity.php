<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'universe_chat')]
#[ORM\Entity(repositoryClass: \App\Repository\UniverseChatRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_chat_direct', columns: ['direct_key'])]
class UniverseChatEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\JoinTable(name: 'chat_user')]
    #[ORM\ManyToMany(targetEntity: \App\Entity\User::class)]
    private $participants;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $directKey = null;
    public function getDirectKey(): ?string { return $this->directKey; }
    public function setDirectKey(?string $key): void { $this->directKey=$key; }

    public function __construct() { $this->participants = new \Doctrine\Common\Collections\ArrayCollection(); }

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $participants
     */
    public function setParticipants($participants): void
    {
        $this->participants = $participants;
    }
}
