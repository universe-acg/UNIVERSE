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

/**
 * @ORM\Entity(repositoryClass="App\Repository\MenuElementRepository")
 * @ORM\Table(name="universe_menu_element")
 * @Assert\Callback({"App\Validation\Validator", "validate"})
 */
class MenuElement
{


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max = 50)
     */
    protected $icon;

    /**
     * @Gedmo\Slug(fields={"label"}, updatable=true)
     * @ORM\Column(length=255, unique=true)
     */
    protected $slug;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(max = 255)
     */
    protected $link;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @EnglishOnly
     * @Profanity
     * @Assert\Length(max = 255)
     */
    protected $customLink;
    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     * @EnglishOnly
     * @Profanity
     */
    private $label;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="menuElements")
     */
    private $menu;
    /**
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
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

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public function getMenu()
    {
        return $this->menu;
    }

    public function setMenu($menu)
    {
        $this->menu = $menu;

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

    public function getCustomLink()
    {
        return $this->customLink;
    }

    public function setCustomLink($customLink)
    {
        $this->customLink = $customLink;

        return $this;
    }

}
