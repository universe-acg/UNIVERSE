<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\AudienceTranslationRepository")
     * @ORM\Table(name="universe_audience_translation")
     */
    class AudienceTranslation
    {
        
        use ORMBehaviors\Translatable\Translation;
        
        /**
         * @ORM\Column(type="string", length=50, nullable=false)
         * @Assert\NotBlank
         * @Assert\Length(min = 1, max = 50)
         */
        protected $name;
        
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
        
        public function getSlug()
        {
            return $this->slug;
        }
        
        public function setSlug($slug = null)
        {
            if ($slug === null) {
                $slug = $this->generateUniqueSlug();
            }
            $this->slug = $slug;
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
