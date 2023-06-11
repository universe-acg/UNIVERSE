<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\BlogPostTranslationRepository")
     * @ORM\Table(name="universe_blog_post_translation")
     */
    class BlogPostTranslation
    {
        
        use ORMBehaviors\Translatable\Translation;
        
        /**
         * @ORM\Column(type="string", length=80, nullable=false)
         * @Assert\NotBlank
         * @Assert\Length(min = 1, max = 80)
         */
        protected $name;
        
        /**
         * @ORM\Column(type="text", nullable=false)
         * @Assert\NotBlank
         * @Assert\Length(min = 10)
         */
        protected $content;
        /**
         * @Gedmo\Slug(fields={"name"}, updatable=false)
         * @ORM\Column(length=100, unique=true)
         */
        protected $slug;
        /**
         * @var string
         *
         * @ORM\Column(type="string", length=500, nullable=true)
         * @Assert\Length(max = 500)
         */
        private $tags;
        
        public function getName()
        {
            return $this->name;
        }
        
        public function setName(string $name)
        {
            $this->name = $name;
            
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
        
        public function getContent()
        {
            return $this->content;
        }
        
        public function setContent($content)
        {
            $this->content = $content;
            
            return $this;
        }
        
        public function getTags()
        {
            return $this->tags;
        }
        
        public function setTags($tags)
        {
            $this->tags = $tags;
            
            return $this;
        }
        
    }
