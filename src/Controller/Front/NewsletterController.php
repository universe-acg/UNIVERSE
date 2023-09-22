<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Welp\MailchimpBundle\Event\SubscriberEvent;
use Welp\MailchimpBundle\Subscriber\Subscriber;

class NewsletterController extends AbstractController
{

    /**
     * @Route("/newsletter-subscribe", name="newsletter_subscribe")
     */
    public function subscribe(Request $request, EventDispatcherInterface $eventdispatcher, AppServices $services): JsonResponse
    {

        $subscriber = new Subscriber($request->request->get('email'), [], [
            'language' => $request->getLocale()
        ]);

        try {
            $eventdispatcher->dispatch(
                SubscriberEvent::EVENT_SUBSCRIBE, new SubscriberEvent($services->getSetting('mailchimp_list_id'), $subscriber)
            );
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'An error has occured']);
        }
        return new JsonResponse(['success' => 'You have successfully subscribed to our newsletter']);
    }

}
