<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Command;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class ChatHandler implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection in the clients object storage
        $this->clients->attach($conn);

        // Handle authentication and user identification (you can implement your own logic here)

        // For example, you can pass the user's ID as a query parameter when connecting via WebSocket
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $queryParams);
        $userId = $queryParams['user_id']; // Adjust this based on your authentication method

        // Store the connection in the userConnections array
        if (!isset($this->userConnections[$userId])) {
            $this->userConnections[$userId] = [];
        }
        $this->userConnections[$userId][] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Parse the message to determine the recipient(s)
        $data = json_decode($msg);
        $recipientUserId = $data->recipientUserId;

        // Find the recipient's connections
        if (isset($this->userConnections[$recipientUserId])) {
            foreach ($this->userConnections[$recipientUserId] as $recipientConn) {
                $recipientConn->send($msg); // Send the message to the recipient(s)
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove the connection from the clients object storage
        $this->clients->detach($conn);

        // Handle user connection removal
        foreach ($this->userConnections as $userId => $connections) {
            foreach ($connections as $key => $userConn) {
                if ($userConn === $conn) {
                    unset($this->userConnections[$userId][$key]);
                    break;
                }
            }
        }
    }


    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}