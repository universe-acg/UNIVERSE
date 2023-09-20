<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\EventTranslationRepository")
     * @ORM\Table(name="universe_event_translation")
     */
    class EventTranslation
    {
        
        use ORMBehaviors\Translatable\Translation;
    }
