<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UniverseMessageRepository")
 * @ORM\Table(name="universe_message")
 */
class UniverseMessage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UniverseChatEntity", inversedBy="messages")
     */
    private $universeChat;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sentMessages")
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="receivedMessages")
     */
    private $receiver;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sentAt;

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