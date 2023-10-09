<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
    use DateTime;
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\HelpCenterArticleRepository")
     * @ORM\Table(name="universe_help_center_article")
     * @Assert\Callback({"App\Validation\Validator", "validate"})
     * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
     */
    class HelpCenterArticle
    {
        
        /**
         * @Gedmo\Slug(fields={"title"}, updatable=true)
         * @ORM\Column(length=255, unique=true)
         */
        protected $slug;
        /**
         * @ORM\Column(length=255, unique=true)
         */
        protected $tags;
        /**
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank
         * @Assert\Length(min = 2)
         */
        private $title;
        /**
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank
         * @Assert\Length(min = 2)
         */
        private $content;
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;
        /**
         * @ORM\ManyToOne(targetEntity="HelpCenterCategory", inversedBy="articles", fetch="EAGER")
         */
        private $category;
        /**
         * @var integer
         *
         * @ORM\Column(type="integer", nullable=true)
         */
        private $views = 0;
        /**
         * @ORM\Column(type="boolean")
         * @Assert\NotNull
         */
        private $hidden = false;
        /**
         * @ORM\Column(type="boolean")
         * @Assert\NotNull
         */
        private $featured = false;
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
        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $deletedAt;
        
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
        public function getTags()
        {
            return $this->tags;
        }
        
        /**
         * @param mixed $tags
         */
        public function setTags($tags): void
        {
            $this->tags = $tags;
        }
        
        /**
         * @return mixed
         */
        public function getContent()
        {
            return $this->content;
        }
        
        /**
         * @param mixed $content
         */
        public function setContent($content): void
        {
            $this->content = $content;
        }
        
        public function getId()
        {
            return $this->id;
        }
        
        public function viewed()
        {
            $this->views++;
        }
        
        
        public function getUpdatedAt()
        {
            return $this->updatedAt;
        }
        
        public function setUpdatedAt($updatedAt)
        {
            $this->updatedAt = $updatedAt;
            
            return $this;
        }
        
        public function getFeatured()
        {
            return $this->featured;
        }
        
        public function setFeatured($featured)
        {
            $this->featured = $featured;
            
            return $this;
        }
        
        public function getCreatedAt()
        {
            return $this->createdAt;
        }
        
        public function setCreatedAt($createdAt)
        {
            $this->createdAt = $createdAt;
            
            return $this;
        }
        
        public function getCategory()
        {
            return $this->category;
        }
        
        public function setCategory($category)
        {
            $this->category = $category;
            
            return $this;
        }
        
        public function getViews()
        {
            return $this->views;
        }
        
        public function setViews($views)
        {
            $this->views = $views;
            
            return $this;
        }
        
        public function getHidden()
        {
            return $this->hidden;
        }
        
        public function setHidden($hidden)
        {
            $this->hidden = $hidden;
            
            return $this;
        }
        
        public function getDeletedAt()
        {
            return $this->deletedAt;
        }
        
        public function setDeletedAt($deletedAt)
        {
            $this->deletedAt = $deletedAt;
            
            return $this;
        }
        
    }
