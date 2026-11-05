<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'universe_message')]
#[ORM\Entity(repositoryClass: \App\Repository\UniverseMessageRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_chat_message_client', columns: ['universe_chat_id','sender_id','client_id'])]
class UniverseMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: \App\Entity\UniverseChatEntity::class)]
    private $universeChat;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    private $sender;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    private $receiver;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $sentAt;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $clientId = null;
    public function getClientId(): ?string { return $this->clientId; }
    public function setClientId(?string $id): void { $this->clientId=$id; }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUniverseChat()
    {
        return $this->universeChat;
    }

    /**
     * @param mixed $universeChat
     */
    public function setUniverseChat($universeChat): void
    {
        $this->universeChat = $universeChat;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param mixed $receiver
     */
    public function setReceiver($receiver): void
    {
        $this->receiver = $receiver;
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

    /**
     * @return mixed
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @param mixed $sentAt
     */
    public function setSentAt($sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    // Add getter and setter methods for properties

}
