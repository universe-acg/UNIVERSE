<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Message;

use DateTime;

class PublishEventMessage
{
    private $eventId;
    private $scheduledAt;

    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
        $this->scheduledAt = new DateTime();
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function getScheduledAt(): DateTime
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt($scheduledAt)
    {
        $this->scheduledAt = $scheduledAt;
    }
}