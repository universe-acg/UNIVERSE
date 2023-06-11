<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\MenuTranslationRepository")
     * @ORM\Table(name="universe_menu_translation")
     */
    class MenuTranslation
    {
        
        use ORMBehaviors\Translatable\Translation;
        
        /**
         * @ORM\Column(type="string", length=50, nullable=false)
         */
        protected $name;
        
        /**
         * @ORM\Column(type="string", length=50, nullable=true)
         * @Assert\Length(min = 1, max = 50)
         */
        protected $header;
        
        /**
         * @Gedmo\Slug(fields={"name"}, updatable=true)
         * @ORM\Column(length=80, unique=true)
         */
        protected $slug;
        
        public function getName()
        {
            return $this->name;
        }
        
        public function setName($name)
        {
            $this->name = $name;
            
            return $this;
        }
        
        public function getHeader()
        {
            return $this->header;
        }
        
        public function setHeader($header)
        {
            $this->header = $header;
            
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
