<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UniverseChatUserRepository")
 * @ORM\Table(name="universe_chat_user")
 */
class UniverseChatUser
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="UniverseChatEntity")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     */
    private $universeChat;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}