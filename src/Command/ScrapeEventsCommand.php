<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Command;

use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command("scrape:events")
 */
#[\Symfony\Component\Console\Attribute\AsCommand(name: 'scrape:events')]
class ScrapeEventsCommand extends Command
{
    protected static $defaultName = 'scrape:events';

    protected function configure(): void
    {
        $this
            ->setDescription('Scrape events and store them in the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Your scraping logic here
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.acg.edu/events/');

        // Extract event details, avoid duplicates, and store them in the database
        // ...

        $output->writeln('Events scraped and stored successfully.');

        return Command::SUCCESS;
    }
}
