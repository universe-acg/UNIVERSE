<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebSocketServerCommand extends Command
{
    protected static $defaultName = 'websocket:server';

    protected function configure()
    {
        $this
            ->setDescription('Start the WebSocket server')
            ->setHelp('This command starts the WebSocket server to handle real-time chat.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create(); // Create an event loop

        $chatHandler = new ChatHandler(); // Implement your WebSocket message handling logic
        $webSocketServer = new IoServer(
            new HttpServer(
                new WsServer($chatHandler)
            ),
            new Server('0.0.0.0:8080', $loop) // Use React\Socket\Server here
        );

        $output->writeln('WebSocket server started.');
        $webSocketServer->run();

        return Command::SUCCESS;
    }
}