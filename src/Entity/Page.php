<?php
    
    namespace App\Entity;
    
    use DateTime;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    use ORMBehaviors\Translatable\Translatable;
    use Symfony\Component\PropertyAccess\PropertyAccess;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
     * @ORM\Table(name="universe_page")
     * @Assert\Callback({"App\Validation\Validator", "validate"})
     */
    class Page
    {
        
        use ORMBehaviors\Translatable\Translatable;
        
        /**
         * @Assert\Valid()
         */
        protected $translations;
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;
        /**
         * @var DateTime $updatedAt
         *
         * @Gedmo\Timestampable(on="update")
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $updatedAt;
        
        public function getId()
        {
            return $this->id;
        }
        
        public function __call($method, $arguments)
        {
            return PropertyAccess::createPropertyAccessor()->getValue($this->translate(), $method);
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
        
    }
