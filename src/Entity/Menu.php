<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'universe_menu')]
#[ORM\Entity(repositoryClass: \App\Repository\MenuRepository::class)]
#[Assert\Callback(['App\Validation\Validator', 'validate'])]
class Menu
{


    #[ORM\Column(length: 255, unique: true)]
    protected $slug;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private $name;
    /**
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private $header;
    #[ORM\OneToMany(targetEntity: \App\Entity\MenuElement::class, mappedBy: 'menu', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Assert\Valid]
    private $menuElements;

    public function __construct()
    {
        $this->menuElements = new ArrayCollection();
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
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header): void
    {
        $this->header = $header;
    }

    public function getId()
    {
        return $this->id;
    }


    /**
     * @return Collection|MenuElement[]
     */
    public function getMenuElements(): Collection
    {
        return $this->menuElements;
    }

    public function addMenuElement(MenuElement $menuElement): self
    {
        if (!$this->menuElements->contains($menuElement)) {
            $this->menuElements[] = $menuElement;
            $menuElement->setMenu($this);
        }

        return $this;
    }

    public function removeMenuElement(MenuElement $menuElement): self
    {
        if ($this->menuElements->contains($menuElement)) {
            $this->menuElements->removeElement($menuElement);
// set the owning side to null (unless already changed)
            if ($menuElement->getMenu() === $this) {
                $menuElement->setMenu(null);
            }
        }

        return $this;
    }

}
