<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\PageTranslationRepository")
     * @ORM\Table(name="universe_page_translation")
     */
    class PageTranslation
    {
        
        use ORMBehaviors\Translatable\Translation;
        
        /**
         * @Gedmo\Slug(fields={"title"}, updatable=true)
         * @ORM\Column(length=70, unique=true)
         */
        protected $slug;
        /**
         * @ORM\Column(type="text", nullable=true)
         */
        protected $content;
        /**
         * @ORM\Column(type="string", length=35, nullable=false)
         */
        private $title;
        
        public function getContent()
        {
            return $this->content;
        }
        
        public function setContent($content)
        {
            $this->content = $content;
            
            return $this;
        }
        
        public function getTitle()
        {
            return $this->title;
        }
        
        public function setTitle($title)
        {
            $this->title = $title;
            
            return $this;
        }
        
        public function getSlug()
        {
            return $this->slug;
        }
        
        public function setSlug($slug)
        {
            $this->slug = $slug;
            
            return $this;
        }
        
    }
