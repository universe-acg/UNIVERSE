<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\MenuElementTranslationRepository")
     * @ORM\Table(name="universe_menu_element_translation")
     */
    class MenuElementTranslation
    {
        
        use ORMBehaviors\Translatable\Translation;
        
        /**
         * @ORM\Column(type="string", length=50, nullable=false)
         * @Assert\Length(min = 1, max = 50)
         */
        protected $label;
        
        /**
         * @Gedmo\Slug(fields={"label"}, updatable=true)
         * @ORM\Column(length=80, unique=true)
         */
        protected $slug;
        
        public function getLabel()
        {
            return $this->label;
        }
        
        public function setLabel(string $label): self
        {
            $this->label = $label;
            
            return $this;
        }
        
        public function setName($label)
        {
            $this->label = $label;
            
            return $this;
        }
        
        public function getSlug()
        {
            return $this->slug;
        }
        
        public function setSlug($slug)
        {
            $this->slug = $slug;
        }
        
    }
