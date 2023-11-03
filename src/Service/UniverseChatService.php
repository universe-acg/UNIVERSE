<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\UniverseChat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UniverseChatService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createChat(UniverseChat $chat)
    {
        $this->entityManager->persist($chat);
        $this->entityManager->flush();
    }

    public function getChatById($chatId)
    {
        return $this->entityManager->getRepository(UniverseChat::class)->find($chatId);
    }

    public function getChatsForUser(User $user)
    {
        return $user->getUniverseChats();
    }

    public function addUserToChat(User $user, UniverseChat $chat)
    {
        $chat->addParticipant($user);
        $this->entityManager->persist($chat);
        $this->entityManager->flush();
    }

    public function removeUserFromChat(User $user, UniverseChat $chat)
    {
        $chat->removeParticipant($user);
        $this->entityManager->persist($chat);
        $this->entityManager->flush();
    }

    public function updateChatInfo(UniverseChat $chat, $newChatData)
    {
        // Update chat information, e.g., title and description
        $chat->setTitle($newChatData['title']);
        $chat->setDescription($newChatData['description']);
        $this->entityManager->persist($chat);
        $this->entityManager->flush();
    }

    public function deleteChat(UniverseChat $chat)
    {
        $this->entityManager->remove($chat);
        $this->entityManager->flush();
    }
}