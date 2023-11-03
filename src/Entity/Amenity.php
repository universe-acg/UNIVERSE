<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="universe_amenity")
 * @ORM\Entity(repositoryClass="App\Repository\AmenityRepository")
 */
class Amenity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ManyToMany(targetEntity="Venue", mappedBy="amenities")
     */
    private $venues;
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $icon;
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=80)
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     */
    private $slug;
    /**
     * @ORM\Column(type="boolean")
     */
    private $hidden;
    /**
     * @var DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @var DateTime $updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->venues = new ArrayCollection();
    }

    // Getters and setters for each property...

    /**
     * @return ArrayCollection
     */
    public function getVenues()
    {
        return $this->venues;
    }

    /**
     * @param ArrayCollection $venues
     */
    public function setVenues(ArrayCollection $venues): void
    {
        $this->venues = $venues;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function isHidden()
    {
        return $this->hidden;
    }

    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
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

    private function generateUniqueSlug()
    {
        $baseSlug = // Generate the base slug here, for example, from the title or some unique identifier.
        $salt = uniqid('', true); // Generate a unique salt, you can use other methods if needed.

        // Combine the base slug and salt to create a unique slug.
        $uniqueSlug = $baseSlug . '-' . $salt;

        // You can further sanitize or modify the uniqueSlug if needed.

        return $uniqueSlug;
    }
}