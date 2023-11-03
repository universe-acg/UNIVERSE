<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use App\Validator\Constraints\EnglishOnly;
use App\Validator\Constraints\Profanity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FacilityRepository")
 * @ORM\Table(name="universe_blog_post")
 * @Assert\Callback({"App\Validation\Validator", "validate"})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Vich\Uploadable
 */
class FacilityEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $minPlayers;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxPlayers;

    // Add the additional fields from the SQL query
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $reference;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     */
    private $externallink;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getMinPlayers()
    {
        return $this->minPlayers;
    }

    /**
     * @param mixed $minPlayers
     */
    public function setMinPlayers($minPlayers): void
    {
        $this->minPlayers = $minPlayers;
    }

    /**
     * @return mixed
     */
    public function getMaxPlayers()
    {
        return $this->maxPlayers;
    }

    /**
     * @param mixed $maxPlayers
     */
    public function setMaxPlayers($maxPlayers): void
    {
        $this->maxPlayers = $maxPlayers;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param mixed $views
     */
    public function setViews($views): void
    {
        $this->views = $views;
    }

    /**
     * @return mixed
     */
    public function getExternallink()
    {
        return $this->externallink;
    }

    /**
     * @param mixed $externallink
     */
    public function setExternallink($externallink): void
    {
        $this->externallink = $externallink;
    }

}
