<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\UniverseChat;
use App\Entity\UniverseMessage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UniverseMessageService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createMessage(UniverseMessage $message)
    {
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function sendMessage(UniverseMessage $message)
    {
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function getMessagesForChat(UniverseChat $chat)
    {
        return $this->entityManager->getRepository(UniverseMessage::class)->findBy(['universeChat' => $chat]);
    }

    public function markMessageAsRead(UniverseMessage $message, User $user)
    {
        // Implement logic to mark a message as read for a specific user
    }

    public function deleteMessage(UniverseMessage $message)
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();
    }

    public function editMessage(UniverseMessage $message, $newContent)
    {
        // Implement logic to edit the content of a message
        $message->setContent($newContent);
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function getUnreadMessages(User $user)
    {
        return $this->entityManager->getRepository(UniverseMessage::class)->findUnreadMessagesForUser($user);
    }

    public function getRecentMessages(UniverseChat $chat, $limit = 10)
    {
        return $this->entityManager->getRepository(UniverseMessage::class)->findRecentMessagesForChat($chat, $limit);
    }
}