<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private $twilioClient;

    public function __construct(string $accountSid, string $authToken)
    {
        $this->twilioClient = new Client($accountSid, $authToken);
    }

    public function sendSMS(string $to, string $message)
    {
        $this->twilioClient->messages->create(
            $to,
            [
                'from' => "+306989442438",
                'body' => $message,
            ]
        );
    }

    // Add other methods for Twilio operations here
}