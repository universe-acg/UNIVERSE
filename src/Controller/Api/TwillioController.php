<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwillioController extends AbstractController
{

//    private $twilioService;
//
//    public function __construct(TwilioService $twilioService)
//    {
//        $this->twilioService = $twilioService;
//    }

    /**
     * @Route("/twillio", name="twillio_route", methods="GET")
     */
    public function sendSMS()
    {
        $to = '+306989442438';// Replace with the recipient's phone number
        $message = 'Hello, this is your SMS message!';

//        $this->twilioService->sendSMS($to, $message);

        return new Response('SMS sent successfully');
    }
}