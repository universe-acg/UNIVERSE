<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Controller\Api;
    
    use App\Entity\Order;
    use App\Repository\OrganizerRepository;
    use App\Service\AppServices;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class QrReaderController extends AbstractController
    {
        
        private OrganizerRepository $organizerRepository;
        private AppServices $appServices;
        
        /**
         * @param AppServices $appServices
         */
        public function __construct(AppServices $appServices, OrganizerRepository $organizerRepository)
        {
            $this->appServices = $appServices;
            $this->organizerRepository = $organizerRepository;
        }
        
        /**
         * @Route("/scanqr", name="qr_reader")
         */
        public function qrReader(): Response
        {
            return $this->render('Front/Page/qr_reader.html.twig');
        }
        
        /**
         * @Route("/api/handle-post-request", methods={"POST"})
         */
        public function handlePostRequest(Request $request)
        {
            // Get the JSON data from the request body
            $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            
            // Check if the "code" key exists in the JSON data
            if (isset($requestData['qrdata'])) {
                
                // Retrieve the "code" value
                $code = $requestData['qrdata'];
                
                [$orderId, $orderReference] = explode('/', $code);
                
                // Retrieve the Order entity using the Order repository
                $orderRepository = $this->getDoctrine()->getRepository(Order::class);
                $order = $orderRepository->findOneBy(['id' => $orderId, 'reference' => $orderReference]);
                
                if ($order instanceof Order) {
                    $order->setStatus(2);
                    
                    // Persist the changes to the database
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($order);
                    $entityManager->flush();
                    
                    
                    // update user badge
                    $this->appServices->updateAttendeeBadge($order->getUser());
                    
                    $orderElements = $order->getOrderelements();
                    
                    // update creator badge
                    $matchingOrderElement = null;
                    foreach ($orderElements as $orderElement) {
                        if ($orderElement->getOrder()->getId() === $orderId) {
                            $matchingOrderElement = $orderElement;
                            break;
                        }
                    }
                    
                    if ($matchingOrderElement) {
                        $eventTicket = $matchingOrderElement->getEventticket();
                        
                        if ($eventTicket) {
                            $event = $eventTicket->getEvent();
                            
                            if ($event) {
                                $organizer = $event->getOrganizer();
                                $this->appServices->updateCreatorBadge($organizer->getUser());
                            }
                        }
                    }
                    
                    return new JsonResponse(Response::HTTP_OK);
                }
                
                return new JsonResponse(Response::HTTP_NOT_FOUND);
            }
            
            return new JsonResponse(Response::HTTP_BAD_REQUEST);
        }
    }
