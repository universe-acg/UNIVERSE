<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Entity\UniverseMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/chat/{chatId}/messages", name="message_list")
     */
    public function listMessages(int $chatId): Response
    {
        // List messages for a chat room
        $messages = []; // Retrieve messages for the chat room

        return $this->render('message/list.html.twig', [
            'chatId' => $chatId,
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/chat/{chatId}/send-message", name="message_send")
     */
    public function sendMessage(int $chatId, Request $request): Response
    {
        // Send a message in a chat room
        // Implement message sending logic, including form handling
        if ($request->isMethod('POST')) {
            // Handle message submission and database storage
        }

        return $this->render('message/send.html.twig', [
            'chatId' => $chatId,
        ]);
    }

    /**
     * @Route("/api/message/save", name="save_message", methods={"POST"})
     */
    public function saveMessage(Request $request)
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        // Perform validation and any necessary processing on the message data

        // Create a new Message entity and save it to the database
        $message = new UniverseMessage();
        $message->setSender($data['sender']);
        $message->setRecipient($data['recipient']);
        $message->setContent($data['content']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($message);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Message saved.']);
    }

    /**
     * @Route("/chat/{chatId}/message/{messageId}/edit", name="message_edit")
     */
    public function editMessage(int $chatId, int $messageId, Request $request): Response
    {
        // Edit a sent message
        // Implement message editing logic, including form handling
        if ($request->isMethod('POST')) {
            // Handle message editing and database update
        }

        return $this->render('message/edit.html.twig', [
            'chatId' => $chatId,
            'messageId' => $messageId,
        ]);
    }

    /**
     * @Route("/chat/{chatId}/message/{messageId}/delete", name="message_delete")
     */
    public function deleteMessage(int $chatId, int $messageId): Response
    {
        // Delete a message
        // Implement message deletion logic
        // Perform message deletion and database update

        return $this->redirectToRoute('message_list', ['chatId' => $chatId]);
    }
}