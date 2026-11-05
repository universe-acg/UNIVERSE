<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'universe_chat_user')]
#[ORM\Entity(repositoryClass: \App\Repository\UniverseChatUserRepository::class)]
class UniverseChatUser
{
    #[ORM\JoinColumn(name: 'chat_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \App\Entity\UniverseChatEntity::class)]
    private $universeChat;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    private $user;

    #[ORM\Column(options: ['default'=>0])]
    private int $lastReadMessageId = 0;
    #[ORM\Column(options: ['default'=>false])]
    private bool $muted = false;
    public function getLastReadMessageId(): int { return $this->lastReadMessageId; }
    public function setLastReadMessageId(int $id): void { $this->lastReadMessageId=max($this->lastReadMessageId,$id); }
    public function isMuted(): bool { return $this->muted; }
    public function setMuted(bool $muted): void { $this->muted=$muted; }

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
