<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Service;
    
    use App\Entity\Amenity;
    use App\Entity\AppLayoutSettings;
    use App\Entity\Audience;
    use App\Entity\BlogPost;
    use App\Entity\BlogPostCategory;
    use App\Entity\Category;
    use App\Entity\Event;
    use App\Entity\EventDate;
    use App\Entity\EventTicket;
    use App\Entity\HelpCenterArticle;
    use App\Entity\HelpCenterCategory;
    use App\Entity\Menu;
    use App\Entity\Order;
    use App\Entity\OrderTicket;
    use App\Entity\Page;
    use App\Entity\Review;
    use App\Entity\Settings;
    use App\Entity\Venue;
    use App\Entity\VenueType;
    use DateTime;
    use DateTimeInterface;
    use Doctrine\ORM\EntityManagerInterface;
    use Doctrine\ORM\QueryBuilder;
    use Dompdf\Dompdf;
    use Dompdf\Options;
    use Exception;
    use GeoIp2\Database\Reader;
    use GeoIp2\Exception\AddressNotFoundException;
    use MaxMind\Db\Reader\InvalidDatabaseException;
    use Psr\Cache\InvalidArgumentException;
    use Swift_Attachment;
    use Swift_Mailer;
    use Swift_Message;
    use Symfony\Component\Cache\Adapter\AdapterInterface;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\Routing\Exception\ResourceNotFoundException;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
    use Symfony\Component\Security\Core\Security;
    use Twig\Error\LoaderError;
    use Twig\Error\RuntimeError;
    use Twig\Error\SyntaxError;
    use Twig_Environment;
    
    class AppServices
    {
        private $em;
        private $authChecker;
        private $requestStack;
        private $kernel;
        private $cache;
        private $router;
        private $security;
        private $mailer;
        private $params;
        private $templating;
        private $urlMatcherInterface;
        
        public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, RequestStack $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface)
        {
            $this->em = $entityManager;
            $this->authChecker = $authChecker;
            $this->requestStack = $requestStack;
            $this->kernel = $kernel;
            $this->cache = $cache;
            $this->router = $router;
            $this->security = $security;
            $this->mailer = $mailer;
            $this->params = $params;
            $this->templating = $templating;
            $this->urlMatcherInterface = $urlMatcherInterface;
        }
        
        // Redirects to the referer page when available, if not, redirects to the dashboard index
        public function redirectToReferer($alt = null): RedirectResponse
        {
            if ($this->requestStack->getCurrentRequest()->headers->get('referer')) {
                return new RedirectResponse($this->requestStack->getCurrentRequest()->headers->get('referer'));
            }
            
            if ($alt) {
                if ($this->authChecker->isGranted('ROLE_ADMINISTRATOR')) {
                    return new RedirectResponse($this->router->generate('dashboard_administrator_' . $alt));
                }
                
                if ($this->authChecker->isGranted('ROLE_ORGANIZER')) {
                    return new RedirectResponse($this->router->generate('dashboard_organizer_' . $alt));
                }
                
                if ($this->authChecker->isGranted('ROLE_ATTENDEE')) {
                    return new RedirectResponse($this->router->generate('dashboard_attendee_' . $alt));
                }
                
                return new RedirectResponse($this->router->generate($alt));
            }
            
            return new RedirectResponse($this->router->generate('dashboard_index'));
        }
        
        // Generates a random string iwth a specified length
        
        /**
         * @throws InvalidArgumentException
         */
        public function setSetting($key, $value): int
        {
            $setting = $this->em->getRepository(Settings::class)->findOneByKey($key);
            if ($setting) {
                $setting->setValue($value);
                $this->em->flush();
                $settingcache = $this->cache->getItem('settings_' . $key);
                $settingcache->set($value);
                $this->cache->save($settingcache);
                
                if ($key == "website_name" || $key == "no_reply_email" || $key == "website_root_url") {
                    $this->updateEnv(strtoupper($key), $value);
                }
                
                return 1;
            }
            
            return 0;
        }
        
        // Returns the current user location
        public function updateEnv($key, $value): void
        {
            if ($key == '') {
                return;
            }
            $value = trim($value);
            if ($value == trim($value) && str_contains($value, ' ')) {
                $value = '"' . $value . '"';
            }
            
            $envFile = $this->kernel->getProjectDir() . '/.env';
            $lines = file($envFile);
            $newLines = [];
            foreach ($lines as $line) {
                preg_match('/' . $key . '=/i', $line, $matches);
                if (!count($matches)) {
                    $newLines[] = $line;
                    continue;
                }
                $newLine = trim($key) . "=" . $value . "\n";
                $newLines[] = $newLine;
            }
            $newContent = implode('', $newLines);
            file_put_contents($envFile, $newContent);
        }
        
        // Gets a setting from the cache / db
        
        public function getEnv($key)
        {
            if ($key == '') {
                return;
            }
            $envFile = $this->kernel->getProjectDir() . '/.env';
            $lines = file($envFile);
            foreach ($lines as $line) {
                preg_match('/' . $key . '=/i', $line, $matches);
                
                if (!count($matches)) {
                    continue;
                }
                $value = trim(explode("=", $line, 2)[1]);
                return trim($value, '"');
            }
            return null;
        }
        
        // Sets a setting from the cache / db
        
        public function emptyCart($user): void
        {
            foreach ($user->getCartelements() as $cartelement) {
                $this->em->remove($cartelement);
            }
            $this->em->flush();
        }
        
        // Updates the .env key with the choosen value
        
        // Gets the value with the entered key from the .env file
        
        /**
         * @param $orderReference
         * @throws InvalidArgumentException
         * @throws LoaderError
         * @throws RuntimeError
         * @throws SyntaxError
         */
        public function handleSuccessfulPayment($orderReference): void
        {
            $order = $this->getOrders(array('status' => 0, 'reference' => $orderReference))->getQuery()->getOneOrNullResult();
            $order->setStatus(1);
            foreach ($order->getOrderElements() as $orderelement) {
                for ($i = 1; $i <= $orderelement->getQuantity(); $i++) {
                    $ticket = new OrderTicket();
                    $ticket->setOrderElement($orderelement);
                    $ticket->setReference($this->generateReference(20));
                    $this->em->persist($ticket);
                }
            }
            $this->em->persist($order);
            $this->em->flush();
            if ($order->getUser()->hasRole("ROLE_ATTENDEE")) {
                $this->sendOrderConfirmationEmail($order, $order->getPayment()->getClientEmail());
            }
        }
        
        // Removes all the specified user cart elements
        
        public function getOrders($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $status = array_key_exists('status', $criterias) ? $criterias['status'] : "all";
            $reference = array_key_exists('reference', $criterias) ? $criterias['reference'] : "all";
            $user = array_key_exists('user', $criterias) ? $criterias['user'] : "all";
            $organizer = array_key_exists('organizer', $criterias) ? $criterias['organizer'] : "all";
            $event = array_key_exists('event', $criterias) ? $criterias['event'] : "all";
            $eventDate = array_key_exists('eventdate', $criterias) ? $criterias['eventdate'] : "all";
            $eventTicket = array_key_exists('eventticket', $criterias) ? $criterias['eventticket'] : "all";
            $upcomingtickets = array_key_exists('upcomingtickets', $criterias) ? $criterias['upcomingtickets'] : "all";
            $datefrom = array_key_exists('datefrom', $criterias) ? $criterias['datefrom'] : "all";
            $dateto = array_key_exists('dateto', $criterias) ? $criterias['dateto'] : "all";
            $paymentgateway = array_key_exists('paymentgateway', $criterias) ? $criterias['paymentgateway'] : "all";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "createdAt";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "DESC";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
            $ordersQuantityByDateStat = array_key_exists('ordersQuantityByDateStat', $criterias) ? $criterias['ordersQuantityByDateStat'] : false;
            $sumOrderElements = array_key_exists('sumOrderElements', $criterias) ? $criterias['sumOrderElements'] : false;
            
            if ($this->authChecker->isGranted("ROLE_ATTENDEE")) {
                $user = $this->security->getUser()->getSlug();
            }
            if ($this->authChecker->isGranted("ROLE_ORGANIZER")) {
                $organizer = $this->security->getUser()->getOrganizer()->getSlug();
            }
            
            return $this->em->getRepository(Order::class)->getOrders($status, $user, $organizer, $event, $eventDate, $eventTicket, $reference, $upcomingtickets, $datefrom, $dateto, $paymentgateway, $sort, $order, $limit, $count, $ordersQuantityByDateStat, $sumOrderElements);
        }
        
        
        public function disableSofDeleteFilterForAdmin($entityManager, $authChecker)
        {
//            $entityManager->getFilters()->enable('softdeleteable');
//            if ($authChecker->isGranted("ROLE_ADMINISTRATOR")) {
//                $entityManager->getFilters()->disable('softdeleteable');
//            }
        }
        
        // Handles all the operations needed after a successful payment processing
        
        /**
         * @throws Exception
         */
        public function generateReference($length): string
        {
            $reference = implode('', [
                bin2hex(random_bytes(2)),
                bin2hex(random_bytes(2)),
                bin2hex(chr((ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
                bin2hex(chr((ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
                bin2hex(random_bytes(2))
            ]);
            
            return strlen($reference) > $length ? substr($reference, 0, $length) : $reference;
        }
        
        // Sends the tickets to the attendee
        
        /**
         * @throws SyntaxError
         * @throws RuntimeError
         * @throws LoaderError|InvalidArgumentException
         */
        public function sendOrderConfirmationEmail($order, $emailTo): int
        {
            $pdfOptions = new Options();
            //$pdfOptions->set('defaultFont', 'Arial');
            $dompdf = new Dompdf($pdfOptions);
            $html = $this->templating->render('Dashboard/Shared/Order/ticket-pdf.html.twig', [
                'order' => $order,
                'eventDateTicketReference' => 'all'
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $ticketsPdfFile = $dompdf->output();
            
            $email = (new Swift_Message('Your tickets from' . ' ' . $this->getSetting('website_name')))
                ->setFrom($this->getSetting('no_reply_email'))
                ->setTo($emailTo)
                ->setBody(
                    $this->templating->render('Dashboard/Shared/Order/confirmation-email.html.twig', ['order' => $order]), 'text/html')
                ->attach(new Swift_Attachment($ticketsPdfFile, $order->getReference() . "-" . "tickets") . '.pdf', 'application/pdf');
            
            $this->mailer->send($email);
            return 1;
        }
        
        // Handles all the operations needed after a failed payment processing
        
        /**
         * @throws InvalidArgumentException
         */
        public function getSetting($key)
        {
            
            $settingcache = $this->cache->getItem('settings_' . $key);
            if ($settingcache->isHit()) {
                return $settingcache->get();
            }
            $setting = $this->em->getRepository(Settings::class)->findOneByKey($key);
            
            if (!$setting) {
                return null;
            }
            
            $settingcache->set($setting->getValue());
            $this->cache->save($settingcache);
            return ($setting ? ($setting->getValue()) : (null));
        }
        
        // Handles all the operations needed after a canceled payment processing
        
        public function handleFailedPayment($orderReference, $note = null): void
        {
            $order = $this->getOrders(array('status' => 0, 'reference' => $orderReference))->getQuery()->getOneOrNullResult();
            $order->setStatus(-2);
            $order->setNote($note);
            $this->em->persist($order);
            $this->em->flush();
        }
        
        // Shows the soft deleted entities for ROLE_ADMINISTRATOR
        
        public function handleCanceledPayment($orderReference, $note = null): void
        {
            $order = $this->getOrders(array('status' => 'all', 'reference' => $orderReference))->getQuery()->getOneOrNullResult();
            foreach ($order->getOrderelements() as $orderElement) {
                foreach ($orderElement->getTicketsReservations() as $ticketReservation) {
                    $this->em->remove($ticketReservation);
                    $this->em->flush();
                }
            }
            $order->setStatus(-1);
            $order->setNote($note);
            $this->em->persist($order);
            $this->em->flush();
        }
        
        // Returns the categories after applying the specified search criterias
        
        public function getLanguages($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "name";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "ASC";
            
            return $this->em->getRepository(Language::class)->getLanguages($hidden, $keyword, $slug, $limit, $sort, $order);
        }
        
        // Returns the countries after applying the specified search criterias
        
        public function getAudiences($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "name";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "ASC";
            
            return $this->em->getRepository(Audience::class)->getAudiences($hidden, $keyword, $slug, $limit, $sort, $order);
        }
        
        // Returns the languages after applying the specified search criterias
        
        public function getVenuesTypes($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "name";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "ASC";
            $hasvenues = array_key_exists('hasvenues', $criterias) ? $criterias['hasvenues'] : "all";
            return $this->em->getRepository(VenueType::class)->getVenuesTypes($hidden, $keyword, $slug, $limit, $sort, $order, $hasvenues);
        }
        
        // Returns the audiences after applying the specified search criterias
        
        public function getAmenities($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "name";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "ASC";
            
            return $this->em->getRepository(Amenity::class)->getAmenities($hidden, $keyword, $slug, $limit, $sort, $order);
        }
        
        // Returns the venues types after applying the specified search criterias
        
        public function getReviews($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $user = array_key_exists('user', $criterias) ? $criterias['user'] : "all";
            $event = array_key_exists('event', $criterias) ? $criterias['event'] : "all";
            $organizer = array_key_exists('organizer', $criterias) ? $criterias['organizer'] : "all";
            $visible = array_key_exists('visible', $criterias) ? $criterias['visible'] : true;
            $rating = array_key_exists('rating', $criterias) ? $criterias['rating'] : "all";
            $minrating = array_key_exists('minrating', $criterias) ? $criterias['minrating'] : "all";
            $maxrating = array_key_exists('maxrating', $criterias) ? $criterias['maxrating'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "createdAt";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "DESC";
            
            return $this->em->getRepository(Review::class)->getReviews($keyword, $slug, $user, $event, $organizer, $visible, $rating, $minrating, $maxrating, $limit, $count, $sort, $order);
        }
        
        // Returns the amenities after applying the specified search criterias
        
        public function getBlogPostCategories($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "ASC";
            
            return $this->em->getRepository(BlogPostCategory::class)->getBlogPostCategories($hidden, $keyword, $slug, $limit, $order, $sort);
        }
        
        // Returns the venues after applying the specified search criterias
        
        public function getPaymentGateways($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $organizer = $criterias['organizer'] ?? null;
            $enabled = array_key_exists('enabled', $criterias) ? $criterias['enabled'] : true;
            $gatewayFactoryName = array_key_exists('gatewayFactoryName', $criterias) ? $criterias['gatewayFactoryName'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "number";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "ASC";
            $organizerPayoutPaypalEnabled = $this->getSetting("organizer_payout_paypal_enabled");
            $organizerPayoutStripeEnabled = $this->getSetting("organizer_payout_stripe_enabled");
            
            return $this->em->getRepository(PaymentGateway::class)->getPaymentGateways($organizer, $enabled, $gatewayFactoryName, $slug, $sort, $order, $organizerPayoutPaypalEnabled, $organizerPayoutStripeEnabled);
        }
        
        // Returns the events after applying the specified search criterias
        
        public function getPayments($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $number = array_key_exists('number', $criterias) ? $criterias['number'] : "all";
            
            return $this->em->getRepository(Payment::class)->getPayments($number);
        }
        
        // Returns the users after applying the specified search criterias
        
        public function getEventDates($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $reference = array_key_exists('reference', $criterias) ? $criterias['reference'] : "all";
            $organizer = array_key_exists('organizer', $criterias) ? $criterias['organizer'] : "all";
            $event = array_key_exists('event', $criterias) ? $criterias['event'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
            
            return $this->em->getRepository(EventDate::class)->getEventDates($reference, $organizer, $event, $limit, $count);
        }
        
        // Returns the reviews after applying the specified search criterias
        
        public function getEventTickets($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $reference = array_key_exists('reference', $criterias) ? $criterias['reference'] : "all";
            $organizer = array_key_exists('organizer', $criterias) ? $criterias['organizer'] : "all";
            $event = array_key_exists('event', $criterias) ? $criterias['event'] : "all";
            $eventdate = array_key_exists('eventdate', $criterias) ? $criterias['eventdate'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            
            return $this->em->getRepository(EventTicket::class)->getEventtickets($reference, $organizer, $event, $eventdate, $limit);
        }
        
        // Returns the blog posts categories after applying the specified search criterias
        
        public function getOrderTickets($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $reference = array_key_exists('reference', $criterias) ? $criterias['reference'] : "all";
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $eventDate = array_key_exists('eventdate', $criterias) ? $criterias['eventdate'] : "all";
            $checkedin = array_key_exists('checkedin', $criterias) ? $criterias['checkedin'] : "all";
            return $this->em->getRepository(OrderTicket::class)->getOrderTickets($reference, $keyword, $eventDate, $checkedin);
        }
        
        // Returns the blog posts after applying the specified search criterias
        
        public function getPayoutRequests($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $reference = array_key_exists('reference', $criterias) ? $criterias['reference'] : "all";
            $eventdate = array_key_exists('eventdate', $criterias) ? $criterias['eventdate'] : "all";
            $organizer = array_key_exists('organizer', $criterias) ? $criterias['organizer'] : "all";
            if ($this->authChecker->isGranted("ROLE_ORGANIZER")) {
                $organizer = $this->security->getUser()->getOrganizer()->getSlug();
            }
            $datefrom = array_key_exists('datefrom', $criterias) ? $criterias['datefrom'] : "all";
            $dateto = array_key_exists('dateto', $criterias) ? $criterias['dateto'] : "all";
            $status = array_key_exists('status', $criterias) ? $criterias['status'] : "all";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "createdAt";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "DESC";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
            return $this->em->getRepository(PayoutRequest::class)->getPayoutRequests($reference, $eventdate, $organizer, $datefrom, $dateto, $status, $sort, $order, $limit, $count);
        }
        
        // Returns the pages after applying the specified search criterias
        
        /**
         * @throws RuntimeError
         * @throws SyntaxError
         * @throws LoaderError|InvalidArgumentException
         */
        public function sendPayoutProcessedEmail($payoutRequest, $emailTo): int
        {
            $email = (new Swift_Message('Your payout request has been processed'))
                ->setFrom($this->getSetting('no_reply_email'))
                ->setTo($emailTo)
                ->setBody(
                    $this->templating->render('Dashboard/Shared/Payout/payout-processed-email.html.twig', ['payoutRequest' => $payoutRequest]), 'text/html');
            
            $this->mailer->send($email);
            return 1;
        }
        
        // Returns the help center categories after applying the specified search criterias
        
        public function getAppLayoutSettings()
        {
            return $this->em->getRepository(AppLayoutSettings::class)->find(1);
        }
        
        // Returns the help center articles after applying the specified search criterias
        
        public function getMenus($criterias)
        {
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            return $this->em->getRepository(Menu::class)->getMenus($slug);
        }
        
        // Returns the payment gateways after applying the specified search criterias
        
        /**
         * @throws InvalidDatabaseException
         */
        public function getLinks(): array
        {
            $linksArray = array();
            
            // Add static pages urls
            $staticPages = $this->getPages(array())->getQuery()->getResult();
            $staticPagesArray = array();
            $staticPagesArray['Homepage'] = $this->router->generate('homepage');
            foreach ($staticPages as $staticPage) {
                $staticPagesArray[$staticPage->getTitle()] = $this->router->generate('page', ['slug' => $staticPage->getSlug()]);
            }
            $staticPagesArray['Contact'] = $this->router->generate('contact');
            $linksArray["Static Pages"] = $staticPagesArray;
            
            // Add authentification pages urls
            $authentificationPagesArray = array();
            $authentificationPagesArray['Login'] = $this->router->generate('fos_user_security_login');
            $authentificationPagesArray['Password Resetting'] = $this->router->generate('fos_user_resetting_request');
            $authentificationPagesArray['Attendee Registration'] = $this->router->generate('fos_user_registration_register_attendee');
            $authentificationPagesArray['Organizer Registration'] = $this->router->generate('fos_user_registration_register_organizer');
            $linksArray["Authentification Pages"] = $authentificationPagesArray;
            
            // Add dashboard pages urls
            $dashboardPagesArray = array();
            $dashboardPagesArray['Attendee tickets'] = $this->router->generate('dashboard_attendee_orders');
            $dashboardPagesArray['Create event'] = $this->router->generate('dashboard_organizer_event_add');
            $linksArray["Dashboard Pages"] = $dashboardPagesArray;
            
            // Add Category pages urls
            $categoryPagesArray = array();
            $categoryPagesArray['Categories page'] = $this->router->generate('categories');
            $categoryPagesArray['No link, display featured categories dropdown on hover (header menu only)'] = 'categories_dropdown';
            $categoryPagesArray['Display top 4 featured categories (footer section menu only)'] = 'footer_categories_section';
            $categories = $this->getCategories(array())->getQuery()->getResult();
            foreach ($categories as $category) {
                $categoryPagesArray['Category' . ' - ' . $category->getName()] = $this->router->generate('events', ['category' => $category->getSlug()]);
            }
            $linksArray["Event Categories"] = $categoryPagesArray;
            
            // Add Blog post pages urls
            $blogPagesArray = array();
            $blogPagesArray['Blog page'] = $this->router->generate('blog');
            $blogPosts = $this->getBlogPosts(array())->getQuery()->getResult();
            foreach ($blogPosts as $blogPost) {
                $blogPagesArray['Blog post' . ' - ' . $blogPost->getName()] = $this->router->generate('blog_article', ['slug' => $blogPost->getSlug()]);
            }
            $linksArray["Blog Pages"] = $blogPagesArray;
            
            // Add event pages urls
            $eventPagesArray = array();
            $eventPagesArray['Events page'] = $this->router->generate('events');
            $events = $this->getEvents(array())->getQuery()->getResult();
            foreach ($events as $event) {
                $eventPagesArray['Event' . ' - ' . $event->getName()] = $this->router->generate('event', ['slug' => $event->getSlug()]);
            }
            $linksArray["Events Pages"] = $eventPagesArray;
            
            // Add help center pages urls
            $helpCenterPagesArray = array();
            $helpCenterPagesArray['Help Center page'] = $this->router->generate('help_center');
            $helpCenterCategories = $this->getHelpCenterCategories(array())->getQuery()->getResult();
            $helpCenterArticles = $this->getHelpCenterArticles(array())->getQuery()->getResult();
            foreach ($helpCenterCategories as $helpCenterCategory) {
                $helpCenterPagesArray['Help Center Category' . ' - ' . $helpCenterCategory->getName()] = $this->router->generate('help_center_category', ['slug' => $helpCenterCategory->getSlug()]);
            }
            foreach ($helpCenterArticles as $helpCenterArticle) {
                $helpCenterPagesArray['Help Center Article' . ' - ' . $helpCenterArticle->getTitle()] = $this->router->generate('help_center_article', ['slug' => $helpCenterArticle->getSlug()]);
            }
            $linksArray["Help Center Pages"] = $helpCenterPagesArray;
            
            // Add organizers pages urls
            $organizersPagesArray = array();
            $organizers = $this->getUsers(array("role" => "organizer"))->getQuery()->getResult();
            foreach ($organizers as $organizer) {
                $organizersPagesArray['Organizer Profile' . ' - ' . $organizer->getOrganizer()->getName()] = $this->router->generate('organizer', ['slug' => $organizer->getOrganizer()->getSlug()]);
            }
            $linksArray["Organizers Pages"] = $organizersPagesArray;
            
            // Add venues pages urls
            $venuesPagesArray = array();
            $venuesPagesArray['Venues page'] = $this->router->generate('venues');
            $venues = $this->getVenues(array())->getQuery()->getResult();
            foreach ($venues as $venue) {
                $venuesPagesArray['Venue' . ' - ' . $venue->getName()] = $this->router->generate('venue', ['slug' => $venue->getSlug()]);
            }
            $linksArray["Venues Pages"] = $venuesPagesArray;
            
            return $linksArray;
        }
        
        // Returns the orders after applying the specified search criterias
        
        public function getPages($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            
            return $this->em->getRepository(Page::class)->getPages($slug);
        }
        
        // Returns the payments after applying the specified search criterias
        
        public function getCategories($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $featured = array_key_exists('featured', $criterias) ? $criterias['featured'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "all";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "ASC";
            return $this->em->getRepository(Category::class)->getCategories($hidden, $keyword, $slug, $featured, $limit, $sort, $order);
        }
        
        // Returns the event dates after applying the specified search criterias
        
        public function getBlogPosts($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $selecttags = array_key_exists('selecttags', $criterias) ? $criterias['selecttags'] : false;
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $category = array_key_exists('category', $criterias) ? $criterias['category'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $otherthan = array_key_exists('otherthan', $criterias) ? $criterias['otherthan'] : "all";
            $sort = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
            $order = array_key_exists('order', $criterias) ? $criterias['sort'] : "DESC";
            
            return $this->em->getRepository(BlogPost::class)->getBlogPosts($selecttags, $hidden, $keyword, $slug, $category, $limit, $sort, $order, $otherthan);
        }
        
        // Returns the event tickets after applying the specified search criterias
        
        /**
         * @param $criterias
         * @return QueryBuilder
         * @throws Exception
         */
        public function getEvents($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            
            // Extract criteria variables
            $category = $criterias['category'] ?? "all";
            $venue = $criterias['venue'] ?? "all";
            $organizer = $criterias['organizer'] ?? "all";
            $keyword = $criterias['keyword'] ?? "all";
            $onlineonly = $criterias['onlineonly'] ?? false;
            $pricemin = $criterias['pricemin'] ?? "all";
            $pricemax = $criterias['pricemax'] ?? "all";
            $audience = $criterias['audience'] ?? "all";
            $startDateFilter = $criterias['startdate'] ?? "all";
            $startdatemin = $criterias['startdatemin'] ?? "all";
            $startdatemax = $criterias['startdatemax'] ?? "all";
            $slug = $criterias['slug'] ?? "all";
            $otherthan = $criterias['otherthan'] ?? "all";
            $published = $criterias['published'] ?? true;
            $sort = $criterias['sort'] ?? "eventdates.startdate";
            $order = $criterias['order'] ?? "ASC";
            $limit = $criterias['limit'] ?? "all";
            $count = $criterias['count'] ?? false;
            $elapsed = $criterias['elapsed'] ?? false;
            
            // Create the query builder
            $queryBuilder = $this->em->getRepository(Event::class)->createQueryBuilder('e');
            $queryBuilder
                ->leftJoin('e.eventdates', 'eventdates');
            
            // Add conditions based on criteria
            if ($category !== "all") {
                $queryBuilder
                    ->andWhere('e.category = :category')
                    ->setParameter('category', $category);
            }
            if ($slug !== "all") {
                // Add a condition for the slug
                $queryBuilder
                    ->andWhere('e.slug = :slug')
                    ->setParameter('slug', $slug);
            }
            if ($organizer !== "all") {
                $queryBuilder->leftJoin("e.organizer", "organizer");
            }
            if ($organizer !== "all") {
                $queryBuilder->andWhere("organizer.slug = :organizer")->setParameter("organizer", $organizer);
            }

//        if ($localonly) {
//            // Add a condition for local-only events
//            $queryBuilder->andWhere('e.isLocal = :isLocal')->setParameter('isLocal', true);
//        }

//        if ($location !== "all") {
//             Add a condition for the location
//            $queryBuilder
//                ->andWhere('e.location = :location')
//                ->setParameter('location', $location);
//        }
            
            $queryBuilder->andWhere('e.published = :published')->setParameter('published', $published);
            
            
            if ($category !== "all") {
                // Add a condition for the category
                $queryBuilder
                    ->andWhere('e.category = :category')
                    ->setParameter('category', $category);
            }
//
//        if ($venue !== "all") {
//            // Add a condition for the venue
//            $queryBuilder
//                ->andWhere('e.venue = :venue')
//                ->setParameter('venue', $venue);
//        }
//
//        if ($country !== "all") {
//            // Add a condition for the country
//            $queryBuilder
//                ->andWhere('e.country = :country')
//                ->setParameter('country', $country);
//        }
            
            if ($keyword !== "all") {
                // Add a condition for the keyword
                $queryBuilder
                    ->andWhere('e.name LIKE :keyword OR e.description LIKE :keyword')
                    ->setParameter('keyword', '%' . $keyword . '%');
            }
            
            if ($pricemin !== "all") {
                // Add a condition for the minimum price
                $queryBuilder
                    ->andWhere('e.price >= :pricemin')
                    ->setParameter('pricemin', $pricemin);
            }
            
            if ($pricemax !== "all") {
                // Add a condition for the maximum price
                $queryBuilder
                    ->andWhere('e.price <= :pricemax')
                    ->setParameter('pricemax', $pricemax);
            }
            
            if ($audience !== "all") {
                // Add a condition for the audience
                $queryBuilder
                    ->andWhere('e.audience = :audience')
                    ->setParameter('audience', $audience);
            }
            
            // Add date range filtering
            $startDateMin = null;
            $startDateMax = null;
            
            if ($startDateFilter === "today") {
                $startDateMin = new DateTime('today');
                $startDateMax = new DateTime('today');
            } elseif ($startDateFilter === "tomorrow") {
                $startDateMin = new DateTime('tomorrow');
                $startDateMax = new DateTime('tomorrow');
            } elseif ($startDateFilter === "thisweekend") {
                $startDateMin = new DateTime('saturday this week');
                $startDateMax = new DateTime('sunday this week');
            } elseif ($startDateFilter === "thisweek") {
                $startDateMin = new DateTime('monday this week');
                $startDateMax = new DateTime('sunday this week');
            } elseif ($startDateFilter === "nextweek") {
                $startDateMin = new DateTime('monday next week');
                $startDateMax = new DateTime('sunday next week');
            } elseif ($startDateFilter === "thismonth") {
                $startDateMin = new DateTime('first day of this month');
                $startDateMax = new DateTime('last day of this month');
            } elseif ($startDateFilter === "nextmonth") {
                $startDateMin = new DateTime('first day of next month');
                $startDateMax = new DateTime('last day of next month');
            } elseif ($startDateFilter === "pickadate") {
                // Handle custom date selection logic here if needed
                $customDate = $criterias['custom_date'] ?? null;
                if ($customDate) {
                    $startDateMin = new DateTime($customDate);
                    $startDateMax = new DateTime($customDate);
                }
            }
            
            if ($startDateMin && $startDateMax) {
                $queryBuilder
                    ->andWhere('eventdates.startdate >= :startDateMin')
                    ->andWhere('eventdates.startdate <= :startDateMax')
                    ->setParameter('startDateMin', $startDateMin)
                    ->setParameter('startDateMax', $startDateMax);
            }
            if ($elapsed !== "all") {
                if ($elapsed === true || $elapsed == "1") {
                    $queryBuilder->andWhere("eventdates.startdate < CURRENT_TIMESTAMP()");
                } else if ($elapsed === false || $elapsed == "0") {
                    $queryBuilder->andWhere("eventdates.startdate >= CURRENT_TIMESTAMP()");
                }
            }
            // Execute the query
            return $queryBuilder;
        }
        
        public function getHelpCenterCategories($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $parent = array_key_exists('parent', $criterias) ? $criterias['parent'] : "all";
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "ASC";
            
            $order = "articlescount";
            
            return $this->em->getRepository(HelpCenterCategory::class)->getHelpCenterCategories($parent, $hidden, $keyword, $slug, $limit, $order, $sort);
        }
        
        public function getHelpCenterArticles($criterias)
        {
            // Disable soft delete filter if necessary
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            
            // Extract criteria parameters from the input array
            $selecttags = $criterias['selecttags'] ?? false;
            $hidden = $criterias['hidden'] ?? false;
            $featured = $criterias['featured'] ?? "all";
            $keyword = $criterias['keyword'] ?? "all";
            $slug = $criterias['slug'] ?? "all";
            $category = $criterias['category'] ?? "all";
            $limit = $criterias['limit'] ?? "all";
            $otherthan = $criterias['otherthan'] ?? "all";
            $sort = $criterias['sort'] ?? "DESC";
            $order = $criterias['order'] ?? "createdAt";
            
            // Call the repository method to fetch Help Center articles
            return $this->em->getRepository(HelpCenterArticle::class)->getHelpCenterArticles(
                $selecttags,
                $hidden,
                $featured,
                $keyword,
                $slug,
                $category,
                $limit,
                $sort,
                $order,
                $otherthan
            );
        }
        
        // Returns the bought tickets after applying the specified search criterias
        
        public function getUsers($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $role = array_key_exists('role', $criterias) ? $criterias['role'] : "all";
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $createdbyorganizerslug = array_key_exists('createdbyorganizerslug', $criterias) ? $criterias['createdbyorganizerslug'] : "all";
            $organizername = array_key_exists('organizername', $criterias) ? $criterias['organizername'] : "all";
            $organizerslug = array_key_exists('organizerslug', $criterias) ? $criterias['organizerslug'] : "all";
            $username = array_key_exists('username', $criterias) ? $criterias['username'] : "all";
            $email = array_key_exists('email', $criterias) ? $criterias['email'] : "all";
            $firstname = array_key_exists('firstname', $criterias) ? $criterias['firstname'] : "all";
            $lastname = array_key_exists('lastname', $criterias) ? $criterias['lastname'] : "all";
            $enabled = array_key_exists('enabled', $criterias) ? $criterias['enabled'] : true;
            $countryslug = array_key_exists('countryslug', $criterias) ? $criterias['countryslug'] : "all";
            $followedby = array_key_exists('followedby', $criterias) ? $criterias['followedby'] : "all";
            $hasboughtticketforEvent = array_key_exists('hasboughtticketfor', $criterias) ? $criterias['hasboughtticketfor'] : "all";
            $hasboughtticketforOrganizer = array_key_exists('hasboughtticketfororganizer', $criterias) ? $criterias['hasboughtticketfororganizer'] : "all";
            $apiKey = array_key_exists('apikey', $criterias) ? $criterias['apikey'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $isOnHomepageSlider = array_key_exists('isOnHomepageSlider', $criterias) ? $criterias['isOnHomepageSlider'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
            
            return $this->em->getRepository("App\Entity\User")->getUsers($role, $keyword, $createdbyorganizerslug, $organizername, $organizerslug, $username, $email, $firstname, $lastname, $enabled, $countryslug, $slug, $followedby, $hasboughtticketforEvent, $hasboughtticketforOrganizer, $apiKey, $isOnHomepageSlider, $limit, $count);
        }
        
        // Returns the payout requests after applying the specified search criterias
        
        public function getVenues($criterias)
        {
            $this->disableSofDeleteFilterForAdmin($this->em, $this->authChecker);
            $organizer = array_key_exists('organizer', $criterias) ? $criterias['organizer'] : "all";
            $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
            $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
            $country = array_key_exists('country', $criterias) ? $criterias['country'] : "all";
            $venuetypes = array_key_exists('venuetypes', $criterias) ? $criterias['venuetypes'] : "all";
            $directory = array_key_exists('directory', $criterias) ? $criterias['directory'] : "all";
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;
            $minseatedguests = array_key_exists('minseatedguests', $criterias) ? $criterias['minseatedguests'] : "all";
            $maxseatedguests = array_key_exists('maxseatedguests', $criterias) ? $criterias['maxseatedguests'] : "all";
            $minstandingguests = array_key_exists('minstandingguests', $criterias) ? $criterias['minstandingguests'] : "all";
            $maxstandingguests = array_key_exists('maxstandingguests', $criterias) ? $criterias['maxstandingguests'] : "all";
            $organizerEnabled = array_key_exists('organizerEnabled', $criterias) ? $criterias['organizerEnabled'] : "all";
            
            return $this->em->getRepository(Venue::class)->getVenues($organizer, $hidden, $keyword, $country, $venuetypes, $directory, $slug, $limit, $minseatedguests, $maxseatedguests, $minstandingguests, $maxstandingguests, $count, $organizerEnabled);
        }
        
        // Sends the payout processed email to the organizer
        
        /**
         * @throws InvalidDatabaseException
         */
        public function locateUser(): ?array
        {
            $request = $this->requestStack->getCurrentRequest();
            $reader = new Reader($this->kernel->getProjectDir() . "/assets/vendor/geolite/GeoLite2-City.mmdb");
            try {
                $ip = '128.101.101.101';
                if ($this->kernel->getEnvironment() == "prod") {
                    $ip = $request->getClientIp();
                }
                $record = $reader->city($ip);
                $countryentity = $this->getCountries(array('isocode' => $record->country->isoCode))->getQuery()->getOneOrNullResult();
            } catch (AddressNotFoundException $ex) {
                return null;
                // return new Response("It wasn't possible to retrieve information about the provided IP"));
            }
            
            return array('record' => $record, 'country' => $countryentity);
        }
        
        // Returns the menus (header and footer)
        
        public function getEventCountForToday(): int
        {
            $today = new DateTime('today');
            $qb = $this->getEventsQueryBuilder(['startdate' => $today]);
            
            return (int)$qb->select('COUNT(e.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }
        
        public function getEventsQueryBuilder(array $criteria = []): QueryBuilder
        {
            $queryBuilder = $this->em->createQueryBuilder()
                ->select('e')
                ->from(Event::class, 'e');
            
            $aliasDefined = false; // Flag to check if the alias is already defined
            
            // Apply filters and criteria based on the $criteria array
            // Example: You can add where clauses to $queryBuilder based on the criteria
            
            if (isset($criteria['category']) && $criteria['category'] !== 'all') {
                // Add a category filter
                $queryBuilder->andWhere('e.category = :category')
                    ->setParameter('category', $criteria['category']);
            }
            
            // Check if 'ed' alias is already defined
            $dqlParts = $queryBuilder->getDQLParts();
            if (isset($dqlParts['join']) && isset($dqlParts['join']['e.eventdates'])) {
                $aliasDefined = true;
            }
            
            // Add date range filtering conditions if they are present in $criteria
            if (isset($criteria['startdate']) && isset($criteria['enddate'])) {
                if (!$aliasDefined) {
                    // Only add the left join if the alias is not already defined
                    $queryBuilder->leftJoin('e.eventdates', 'ed'); // Join with eventdates table
                    $aliasDefined = true;
                }
                
                $queryBuilder
                    ->andWhere('ed.startdate >= :startDate')
                    ->andWhere('ed.startdate <= :endDate')
                    ->setParameter('startDate', $criteria['startdate'])
                    ->setParameter('endDate', $criteria['enddate']);
            }
            
            // Add more filters based on your criteria as needed
            
            return $queryBuilder;
        }
        
        public function getEventCountForDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): int
        {
            $query = $this->em->createQuery('
    SELECT COUNT(e.id)
    FROM App\Entity\Event e
        LEFT JOIN App\Entity\EventDate ed WITH e.id = ed.event
    WHERE ed.startdate >= :startDate AND ed.enddate <= :endDate
');
            $query->setParameter('startDate', $startDate);
            $query->setParameter('endDate', $endDate);
            
            return (int)$query->getSingleScalarResult();
        }
        
        
        // Generates a list of pages to be cho $services->getEvents(array("slug" => $slug, "elapsed" => "all", "published" => "all"))->getQuery()sen in a menu element
        
        // Get route name from path
        
        public function getRouteName($path = null)
        {
            try {
                if ($path) {
                    return $this->urlMatcherInterface->match($path)['_route'];
                }
                
                return $this->urlMatcherInterface->match($this->requestStack->getCurrentRequest()->getPathInfo())['_route'];
            } catch (ResourceNotFoundException $e) {
                return null;
            }
        }
        
        // Checks if string ends with string
        public function changeLinkLocale($newLocale, $link)
        {
            if ($link == "categories_dropdown" || $link == "footer_categories_section") {
                return $link;
            }
//        if (str_contains($link, "/")) {
//            return str_replace("/", "/" . $newLocale . "/", $link);
//        }
////
//        if ($this->endsWith($link, "/en")) {
//            return str_replace("/en", "/" . $newLocale, $link);
//        }
            return 'x';
        }
        
        // Changes the link locale
        
        public function endsWith($haystack, $needle): bool
        {
            $length = strlen($needle);
            if (!$length) {
                return true;
            }
            return substr($haystack, -$length) === $needle;
        }
        
        // Returns the currencies
        
        public function getCurrencies($criterias)
        {
            $ccy = array_key_exists('ccy', $criterias) ? $criterias['ccy'] : "all";
            $symbol = array_key_exists('symbol', $criterias) ? $criterias['symbol'] : "all";
            return $this->em->getRepository("App\Entity\Currency")->getCurrencies($ccy, $symbol);
        }
        
        // Returns the current protocol of the current request
        public function getCurrentRequestProtocol(): string
        {
            return $this->requestStack->getCurrentRequest()->getScheme();
        }
        
    }
