<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    /**
     * @Route("/chat", name="chat_index")
     */
    public function index(): Response
    {
        // Display a list of chat rooms
        $chatRooms = []; // Retrieve chat rooms from your database/repository
        return $this->render('Dashboard/Shared/Chat/index.html.twig', [
            'chatRooms' => $chatRooms,
        ]);
    }

    /**
     * @Route("/chat/{chatId}/remove-user/{userId}", name="chat_remove_user")
     */
    public function removeUserFromChat(int $chatId, int $userId): Response
    {
        // Remove a user from an existing chat room
        // Implement user removal from chat logic
        // Perform user removal and database update

        return $this->redirectToRoute('chat_show', ['chatId' => $chatId]);
    }

    /**
     * @Route("/chat/{chatId}/mute-chat", name="chat_mute")
     */
    public function muteChat(int $chatId): Response
    {
        // Mute a chat room
        // Implement chat muting logic
        // Perform chat muting and database update

        return $this->redirectToRoute('chat_show', ['chatId' => $chatId]);
    }

    /**
     * @Route("/chat/create", name="chat_create")
     */
    public function createChat(Request $request): Response
    {
        // Create a new chat room
        // Implement chat creation logic, including form handling
        if ($request->isMethod('POST')) {
            // Handle form submission and database storage
        }

        return $this->render('Dashboard/Shared/Chat/create.html.twig');
    }

    /**
     * @Route("/chat/{chatId}", name="chat_show")
     */
    public function showChat(int $chatId): Response
    {
        // Display a specific chat room
        $chatRoom = []; // Retrieve chat room details from your database/repository
        $messages = []; // Retrieve messages for the chat room

        return $this->render('Dashboard/Shared/Chat/show.html.twig', [
            'chatRoom' => $chatRoom,
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/chat/{chatId}/add-user", name="chat_add_user")
     */
    public function addUserToChat(int $chatId, Request $request): Response
    {
        // Add a user to an existing chat room
        // Implement user addition to chat logic, including form handling
        if ($request->isMethod('POST')) {
            // Handle form submission and database update
        }

        return $this->render('Dashboard/Shared/Chat/add_user.html.twig', [
            'chatId' => $chatId,
        ]);
    }

    // Add more chat-related actions as needed
}