<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    
    /**
     * @ORM\Entity()
     * @ORM\Table(name="universe_payment_gateway_translation")
     */
    class PaymentGatewayTranslation
    {
        
        use ORMBehaviors\Translatable\Translation;
        
        /**
         * @ORM\Column(type="text", nullable=true)
         */
        private $instructions;
        
        public function getInstructions()
        {
            return $this->instructions;
        }
        
        public function setInstructions($instructions)
        {
            $this->instructions = $instructions;
            
            return $this;
        }
        
    }
