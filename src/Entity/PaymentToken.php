<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Payum\Core\Model\Token;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\PaymentTokenRepository")
     * @ORM\Table(name="universe_payment_token")
     */
    class PaymentToken extends Token
    {
    
    }
