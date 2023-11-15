<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use DateTime;
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
        $contacts = [
            [
                'name' => 'John Doe',
                'datecreated' => new DateTime('2023-11-08'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'This is the last message sent to John Doe, which is a longer message that needs to be trimmed on the front end.',
                'isorganizer' => true,
            ],
            [
                'name' => 'Alice Smith',
                'datecreated' => new DateTime('2023-11-07'),
                'unread' => false,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Alice Smith.',
                'isorganizer' => false,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => true,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => false,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => true,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => false,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => true,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => false,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => true,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => false,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => false,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => true,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => false,
            ],
            [
                'name' => 'Bob Johnson',
                'datecreated' => new DateTime('2023-11-06'),
                'unread' => true,
                'profile_image' => 'https://random.imagecdn.app/500/500',
                'last_message' => 'Last message to Bob Johnson.',
                'isorganizer' => false,
            ],
        ];

        $msgs = [
            [
                'issender' => true,
                'msg' => [
                    'contact' => [
                        'name' => 'John Doe',
                        'datetimeSent' => new DateTime('2023-11-08 10:15:00'),
                    ],
                    'txt' => 'This is a message sent by John Doe.',
                ],
            ],
            [
                'issender' => false,
                'msg' => [
                    'contact' => [
                        'name' => 'Alice Smith',
                        'datetimeSent' => new DateTime('2023-11-08 10:30:00'),
                    ],
                    'txt' => 'This is a reply from Administrator.',
                ],
            ],
            [
                'issender' => true,
                'msg' => [
                    'contact' => [
                        'name' => 'John Doe',
                        'datetimeSent' => new DateTime('2023-11-08 11:15:00'),
                    ],
                    'txt' => 'Another message from John Doe.',
                ],
            ],
            [
                'issender' => false,
                'msg' => [
                    'contact' => [
                        'name' => 'Alice Smith',
                        'datetimeSent' => new DateTime('2023-11-08 10:30:00'),
                    ],
                    'txt' => 'This is a reply from Administrator.',
                ],
            ],
            // Add more messages as needed
        ];

        return $this->render('Dashboard/Shared/Chat/index.html.twig', [
            'chatRooms' => $chatRooms,
            'contacts' => $contacts,
            'msgs' => $msgs
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