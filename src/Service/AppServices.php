<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\AppLayoutSettings;
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
use App\Entity\Organizer;
use App\Entity\Page;
use App\Entity\Reel;
use App\Entity\Settings;
use App\Entity\User;
use App\Entity\Venue;
use App\Repository\NotificationRepository;
use App\Repository\OrganizerRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
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

    private $organizerRepository;
    private $notificationService;

    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository, NotificationService $notificationService, EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, RequestStack $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, Twig_Environment $templating, UrlMatcherInterface $urlMatcherInterface, OrganizerRepository $organizerRepository)
    {
        $this->notificationRepository = $notificationRepository;
        $this->notificationService = $notificationService;
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
        $this->organizerRepository = $organizerRepository;
    }

    public function hasUnreadNotifications()
    {
        // Get the count of unread notifications
        $unreadCount = $this->notificationRepository->countUnreadNotificationsByUser($this->security->getUser()->getId());

        // Check if there are unread notifications
        return $unreadCount > 0;
    }

    public function getLastFiveNotifications()
    {
        // Retrieve the last 5 notifications from the repository
        $lastFiveNotifications = $this->notificationRepository->findLastFiveNotificationsByUser($this->security->getUser()->getId());

        return [
            'lastFiveNotifications' => $lastFiveNotifications,
        ];
    }

    public function getLastFiveNotificationsWithUnreadFlag()
    {
        // Retrieve the last 5 notifications from the repository
        $lastFiveNotifications = $this->notificationRepository->findLastFiveNotificationsByUser($this->security->getUser()->getId());

        // Check for unread notifications
        $hasUnreadNotifications = false;
        foreach ($lastFiveNotifications as $notification) {
            if (!$notification->getRead()) {
                $hasUnreadNotifications = true;
                break;
            }
        }

        return [
            'lastFiveNotifications' => $lastFiveNotifications,
            'hasUnreadNotifications' => $hasUnreadNotifications,
        ];
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

    /**
     * @param $orderReference
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function orderticket($orderReference): void
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
            $this->sendOrderConfirmationEmail($order, $order->getUser()->getEmail());
        }
    }

    public function getOrders($criterias)
    {
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

        return $this->em->getRepository(Order::class)->getOrders($status, $user, $organizer, $event, $eventDate, $eventTicket, $reference, $upcomingtickets, $datefrom, $dateto, $sort, $order, $limit, $count, $ordersQuantityByDateStat, $sumOrderElements);
    }

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

        $this->notificationService->createNotification($this->security->getUser()->getId(), $message, $email->getSubject());

        $this->mailer->send($email);
        return 1;
    }

    // Sends the tickets to the attendee

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

    public function getOrdersForEvent($criterias)
    {
        $event = array_key_exists('event', $criterias) ? $criterias['event'] : "all";
        return $this->em->getRepository(Order::class)->getOrdersForEvent($criterias, $event);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError|InvalidArgumentException
     */
    public function sendmaileventhasbeenupdate($event, $emailTo)
    {
        $message = "Important details of this event: " . $event->getName() . ", which you have booked have been changed.";

        $emaildata = [
            $title = $event->getName(),
            $description = $event->getDescription(),
            $startson = $event->getEventdates()[0]->getStartdate()->format('D j M Y, g:i A'),
            $link = "https://universeacg.xyz/event/" . $event->getSlug(),
        ];

        // send email to organizer and user
        $email = (new Swift_Message($this->getSetting('website_name') . ' - ' . $event->getName() . ' event details udpated'))
            ->setFrom($this->getSetting('no_reply_email'))
            ->setTo($emailTo)
            ->setBody(
                $this->templating->render('Dashboard/Shared/Order/event-updated-email.html.twig', ['data' => $emaildata, 'message' => $message]), 'text/html', 'UTF-8');

        $this->notificationService->createNotification($this->security->getUser()->getId(), $message, $email->getSubject());
        try {
            $result = $this->mailer->send($email);
        } catch (Exception $e) {
        }
    }

    public function getEventDates($criterias)
    {

        $reference = array_key_exists('reference', $criterias) ? $criterias['reference'] : "all";
        $organizer = array_key_exists('organizer', $criterias) ? $criterias['organizer'] : "all";
        $event = array_key_exists('event', $criterias) ? $criterias['event'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(EventDate::class)->getEventDates($reference, $organizer, $event, $limit, $count);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError|InvalidArgumentException
     */
    public function sendmaileventhasbeencancelled($event, $emailTo)
    {
        $message = "This event has been cancelled: " . $event->getName() . ".";

        $emaildata = [
            $title = "We are informing you that the organizer has cancelled this event.",
        ];

        // send email to organizer and user
        $email = (new Swift_Message($this->getSetting('website_name') . ' - ' . $event->getName() . ' event details udpated'))
            ->setFrom($this->getSetting('no_reply_email'))
            ->setTo($emailTo)
            ->setBody(
                $this->templating->render('Dashboard/Shared/Order/event-cancelled-email.html.twig', ['data' => $emaildata, 'message' => $message]), 'text/html', 'UTF-8');

        $this->notificationService->createNotification($this->security->getUser()->getId(), $message, $email->getSubject());

        try {
            $result = $this->mailer->send($email);
        } catch (Exception $e) {
        }
    }

    public function cancelticket($orderReference, $note = null): void
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

    public function getBlogPostCategoriesInForm($criterias, $user)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";

        // if user is org
        if (!empty($this->security->getUser()->getOrganizer())) {
            $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";

            // return the users cate
            $orgofuser = $this->organizerRepository->findBy(array("id" => $user->getOrganizer()->getId()))[0]->getCategory();

            if ($orgofuser == "Club") {
                $slug = "club-news";
            } elseif ($orgofuser == "Organization") {
                $slug = "organizations-news";
            } elseif ($orgofuser == "Office") {
                $slug = "offices-news";
            } elseif ($orgofuser == "Society") {
                $slug = "societies-news";
            }

            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "ASC";

            return $this->em->getRepository(BlogPostCategory::class)->getBlogPostCategories($hidden, $keyword, $slug, $limit);
        }

        if ($this->authChecker->isGranted("ROLE_ADMINISTRATOR")) {
            $slug = "all";
            $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
            $order = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
            $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "ASC";
            return $this->em->getRepository(BlogPostCategory::class)->getBlogPostCategories($hidden, $keyword, $slug, $limit);
        }

        $slug = "attendees";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "ASC";
        return $this->em->getRepository(BlogPostCategory::class)->getBlogPostCategories($hidden, $keyword, $slug, $limit);
    }

    // Returns the events after applying the specified search criterias

    // Returns the users after applying the specified search criterias

    public function getBlogPostCategories($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "ASC";

        return $this->em->getRepository(BlogPostCategory::class)->getBlogPostCategories($hidden, $keyword, $slug, $limit);
    }

    public function getEventTickets($criterias)
    {

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

        $reference = array_key_exists('reference', $criterias) ? $criterias['reference'] : "all";
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $eventDate = array_key_exists('eventdate', $criterias) ? $criterias['eventdate'] : "all";
        $checkedin = array_key_exists('checkedin', $criterias) ? $criterias['checkedin'] : "all";
        return $this->em->getRepository(OrderTicket::class)->getOrderTickets($reference, $keyword, $eventDate, $checkedin);
    }

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


    /**
     * @throws InvalidDatabaseException|Exception
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
        $events = $this->getEvents(array());
        foreach ($events as $event) {
            $eventPagesArray['Event' . ' - ' . $event->getName()] = $this->router->generate('event', ['slug' => $event->getSlug()]);
        }
        $linksArray["Events Pages"] = $eventPagesArray;

        // Add help center pages urls
        $helpCenterPagesArray = array();
        $helpCenterPagesArray['Help Center page'] = $this->router->generate('help_center');
        $helpCenterCategories = $this->getHelpCenterCategories(array());
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

        return $linksArray;
    }

    // Returns the orders after applying the specified search criterias

    public function getPages($criterias)
    {

        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";

        return $this->em->getRepository(Page::class)->getPages($slug);
    }

    // Returns the venues after applying the specified search criterias

    public function getCategories($criterias)
    {

        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $featured = array_key_exists('featured', $criterias) ? $criterias['featured'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "all";
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : "ASC";

        return $this->em->getRepository(Category::class)->getCategories($hidden, $keyword, $slug, $featured, $limit, $sort, $order);
    }

    // Returns the venues after applying the specified search criterias

    public function getBlogPosts($criterias)
    {
        $selecttags = array_key_exists('selecttags', $criterias) ? $criterias['selecttags'] : "all";
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $category = array_key_exists('category', $criterias) ? $criterias['category'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $otherthan = array_key_exists('otherthan', $criterias) ? $criterias['otherthan'] : "all";
        $sortedby = array_key_exists('sortedby', $criterias) ? $criterias['sortedby'] : "all";

        return $this->em->getRepository(BlogPost::class)->getBlogPosts($selecttags, $hidden, $keyword, $slug, $category, $limit, $sortedby, $otherthan);
    }

    /**
     * @param $criterias
     * @return QueryBuilder
     * @throws Exception
     */
    public function getEvents($criterias)
    {
        // Extract criteria variables
        $category = $criterias['category'] ?? "all";
        $organizer = $criterias['organizer'] ?? "all";
        $keyword = $criterias['keyword'] ?? "all";
        $open = $criterias['open'] ?? "all";
        $onlineonly = $criterias['onlineonly'] ?? false;
        $startDateFilter = $criterias['startdate'] ?? "all";
        $slug = $criterias['slug'] ?? "all";
        $otherthan = $criterias['otherthan'] ?? "all";
        $published = $criterias['published'] ?? "all";
        $addedtofavoritesby = $criterias['addedtofavoritesby'] ?? "all";
        $sort = $criterias['sort'] ?? "eventdates.startdate";
        $order = $criterias['order'] ?? "ASC";
        $limit = $criterias['limit'] ?? "all";
        $count = $criterias['count'] ?? false;
        $elapsed = $criterias['elapsed'] ?? "all";

        // Create the query builder
        $queryBuilder = $this->em->getRepository(Event::class)->createQueryBuilder('e');

        // Add conditions based on criteria
        if ($category !== "all") {
            $queryBuilder
                ->andWhere('e.category = :category')
                ->setParameter('category', $category);
        }

        if ($addedtofavoritesby !== "all") {
            $queryBuilder->andWhere(":addedtofavoritesbyuser MEMBER OF e.addedtofavoritesby")->setParameter("addedtofavoritesbyuser", $addedtofavoritesby);
        }

        if ($slug !== "all") {
            // Add a condition for the slug
            $queryBuilder
                ->andWhere('e.slug = :slug')
                ->setParameter('slug', $slug);
        }
        if ($organizer !== "all") {
            $queryBuilder->leftJoin("e.organizer", "organizer");
            $queryBuilder->andWhere("organizer.slug = :organizer")
                ->setParameter("organizer", $organizer);
        }

        if ($published !== "all") {
            $queryBuilder->andWhere('e.published = :published')->setParameter('published', $published);
        }

        if ($open !== "all") {
            $queryBuilder->andWhere('e.open = :open')->setParameter('open', $open);
        }


        if ($category !== "all") {
            // Add a condition for the category
            $queryBuilder
                ->andWhere('e.category = :category')
                ->setParameter('category', $category);
        }
        if ($keyword !== "all") {
            // Add a condition for the keyword
            $queryBuilder
                ->andWhere('e.name LIKE :keyword OR e.description LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
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

        if ($otherthan !== "all") {
            $queryBuilder->andWhere("e.slug != :otherthan")->setParameter("otherthan", $otherthan);
        }

        $queryBuilder
            ->leftJoin('e.eventdates', 'eventdates');


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

        $events = $queryBuilder->getQuery()->getResult();

        $eventsToRemove = [];
        $eventsByCommonValue = [];

        // Step 1: Iterate through each event
        foreach ($events as $event) {
            if ($event->isIsrepeat()) {
                $commonValue = $event->getCommonvalue();


                // Step 2: Create an array of events with the same common value
                if (!isset($eventsByCommonValue[$commonValue])) {
                    $eventsByCommonValue[$commonValue] = [];
                }
                $eventsByCommonValue[$commonValue][] = $event;
            }
        }

        // Step 3: Filter and sort events for each common value
        foreach ($eventsByCommonValue as $commonValue => $commonEvents) {
            $filteredEvents = array_filter($commonEvents, static function ($event) {
                return $event->getLatestEndDate() >= new DateTime();
            });

            // Sort the events based on the latest event date
            usort($filteredEvents, static function ($eventA, $eventB) {
                $latestEventDateA = $eventA->getLatestEndDate();
                $latestEventDateB = $eventB->getLatestEndDate();

                if ($latestEventDateA === null && $latestEventDateB === null) {
                    return 0;
                }

                if ($latestEventDateA === null) {
                    return 1;
                }

                if ($latestEventDateB === null) {
                    return -1;
                }

                return $latestEventDateA <=> $latestEventDateB;
            });

            // Step 4: Add the first event to the removal list
            if (count($filteredEvents) > 1) {
                $eventsToRemove = array_merge($eventsToRemove, array_slice($filteredEvents, 1));
            }
        }

        // Step 5: Remove events with the same common value
        $events = array_filter($events, static function ($event) use ($eventsToRemove) {
            return !in_array($event, $eventsToRemove, true);
        });

        $eventsToRemove = [];
        $eventsByCommonValue = [];

        // Step 1: Iterate through each event
        foreach ($events as $event) {
            if ($event->isIsrepeat()) {
                $commonValue = $event->getCommonvalue();


                // Step 2: Create an array of events with the same common value
                if (!isset($eventsByCommonValue[$commonValue])) {
                    $eventsByCommonValue[$commonValue] = [];
                }
                $eventsByCommonValue[$commonValue][] = $event;
            }
        }

        // Step 3: Filter and sort events for each common value
        foreach ($eventsByCommonValue as $commonValue => $commonEvents) {
            $filteredEvents = array_filter($commonEvents, static function ($event) {
                return $event->getLatestEndDate() >= new DateTime();
            });

            // Sort the events based on the latest event date
            usort($filteredEvents, static function ($eventA, $eventB) {
                $latestEventDateA = $eventA->getLatestEndDate();
                $latestEventDateB = $eventB->getLatestEndDate();

                if ($latestEventDateA === null && $latestEventDateB === null) {
                    return 0;
                }

                if ($latestEventDateA === null) {
                    return 1;
                }

                if ($latestEventDateB === null) {
                    return -1;
                }

                return $latestEventDateA <=> $latestEventDateB;
            });

            // Step 4: Add the first event to the removal list
            if (count($filteredEvents) > 1) {
                $eventsToRemove = array_merge($eventsToRemove, array_slice($filteredEvents, 1));
            }
        }

        // Step 5: Remove events with the same common value
        $events = array_filter($events, static function ($event) use ($eventsToRemove) {
            return !in_array($event, $eventsToRemove, true);
        });

        // Execute the query
        return $events;
    }

    public function getHelpCenterCategories($criterias)
    {
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

    // Returns the event dates after applying the specified search criterias

    public function getHelpCenterArticles($criterias)
    {
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

    public function getUsers($criterias)
    {
        $role = array_key_exists('role', $criterias) ? $criterias['role'] : "all";
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $organizername = array_key_exists('organizername', $criterias) ? $criterias['organizername'] : "all";
        $organizerslug = array_key_exists('organizerslug', $criterias) ? $criterias['organizerslug'] : "all";
        $username = array_key_exists('username', $criterias) ? $criterias['username'] : "all";
        $email = array_key_exists('email', $criterias) ? $criterias['email'] : "all";
        $firstname = array_key_exists('firstname', $criterias) ? $criterias['firstname'] : "all";
        $lastname = array_key_exists('lastname', $criterias) ? $criterias['lastname'] : "all";
        $enabled = array_key_exists('enabled', $criterias) ? $criterias['enabled'] : true;
        $followedby = array_key_exists('followedby', $criterias) ? $criterias['followedby'] : "all";
        $apiKey = array_key_exists('apikey', $criterias) ? $criterias['apikey'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $hasboughtticketforEvent = array_key_exists('hasboughtticketfor', $criterias) ? $criterias['hasboughtticketfor'] : "all";
        $hasboughtticketforOrganizer = array_key_exists('hasboughtticketfororganizer', $criterias) ? $criterias['hasboughtticketfororganizer'] : "all";
        $hasbookedticketfor = array_key_exists('hasbookedticketfor', $criterias) ? $criterias['hasbookedticketfor'] : "all";
        $isOnHomepageSlider = array_key_exists('isOnHomepageSlider', $criterias) ? $criterias['isOnHomepageSlider'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository("App\Entity\User")->getUsers($role, $keyword, $organizername, $organizerslug, $username, $email, $firstname, $lastname, $enabled, $slug, $followedby, $hasboughtticketforEvent, $hasboughtticketforOrganizer, $hasbookedticketfor, $apiKey, $isOnHomepageSlider, $limit, $count);
    }

    public function getVenues($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(Venue::class)->getVenues($hidden, $keyword, $slug, $limit, $count);
    }

    public function getVenuessql($criterias)
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository(Venue::class)->getVenuessql($hidden, $keyword, $slug, $limit, $count);
    }

    public function getVenueBySlug($slug)
    {
        return $this->em->getRepository(Venue::class)->findOneBy(['slug' => $slug]);
    }

    public function getBlogPostVideos($criterias)
    {
        $selecttags = array_key_exists('selecttags', $criterias) ? $criterias['selecttags'] : "all";
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $category = array_key_exists('category', $criterias) ? $criterias['category'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $otherthan = array_key_exists('otherthan', $criterias) ? $criterias['otherthan'] : "all";
        $sortedby = array_key_exists('sortedby', $criterias) ? $criterias['sortedby'] : "all";

        return $this->em->getRepository(Reel::class)->getBlogPostVideos($selecttags, $hidden, $keyword, $slug, $category, $limit, $sortedby, $otherthan);
    }

    /**
     * @param $criterias
     * @return QueryBuilder
     * @throws Exception
     */
    public function getEventssql($criterias)
    {
        // Extract criteria variables
        $category = $criterias['category'] ?? "all";
        $organizer = $criterias['organizer'] ?? "all";
        $keyword = $criterias['keyword'] ?? "all";
        $open = $criterias['open'] ?? "all";
        $onlineonly = $criterias['onlineonly'] ?? false;
        $startDateFilter = $criterias['startdate'] ?? "all";
        $slug = $criterias['slug'] ?? "all";
        $otherthan = $criterias['otherthan'] ?? "all";
        $published = $criterias['published'] ?? "all";
        $addedtofavoritesby = $criterias['addedtofavoritesby'] ?? "all";
        $sort = $criterias['sort'] ?? "eventdates.startdate";
        $order = $criterias['order'] ?? "ASC";
        $limit = $criterias['limit'] ?? "all";
        $count = $criterias['count'] ?? false;
        $elapsed = $criterias['elapsed'] ?? "all";

        // Create the query builder
        $queryBuilder = $this->em->getRepository(Event::class)->createQueryBuilder('e');

        // Add conditions based on criteria
        if ($category !== "all") {
            $queryBuilder
                ->andWhere('e.category = :category')
                ->setParameter('category', $category);
        }

        if ($addedtofavoritesby !== "all") {
            $queryBuilder->andWhere(":addedtofavoritesbyuser MEMBER OF e.addedtofavoritesby")->setParameter("addedtofavoritesbyuser", $addedtofavoritesby);
        }

        if ($slug !== "all") {
            // Add a condition for the slug
            $queryBuilder
                ->andWhere('e.slug = :slug')
                ->setParameter('slug', $slug);
        }
        if ($organizer !== "all") {
            $queryBuilder->leftJoin("e.organizer", "organizer");
            $queryBuilder->andWhere("organizer.slug = :organizer")
                ->setParameter("organizer", $organizer);
        }

        if ($published !== "all") {
            $queryBuilder->andWhere('e.published = :published')->setParameter('published', $published);
        }

        if ($open !== "all") {
            $queryBuilder->andWhere('e.open = :open')->setParameter('open', $open);
        }


        if ($category !== "all") {
            // Add a condition for the category
            $queryBuilder
                ->andWhere('e.category = :category')
                ->setParameter('category', $category);
        }
        if ($keyword !== "all") {
            // Add a condition for the keyword
            $queryBuilder
                ->andWhere('e.name LIKE :keyword OR e.description LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
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

        if ($otherthan !== "all") {
            $queryBuilder->andWhere("e.slug != :otherthan")->setParameter("otherthan", $otherthan);
        }

        $queryBuilder
            ->leftJoin('e.eventdates', 'eventdates');


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

        return $queryBuilder;
    }

    public function getCreators($criteria)
    {
        $selectedCategory = array_key_exists('category', $criteria) ? $criteria['category'] : 'all';
        $keyword = array_key_exists('keyword', $criteria) ? $criteria['keyword'] : '';
        $sortedBy = array_key_exists('sortedby', $criteria) ? $criteria['sortedby'] : 'all';
        $level = array_key_exists('level', $criteria) ? $criteria['level'] : 'all';
        $limit = array_key_exists('limit', $criteria) ? $criteria['limit'] : 'all';

        if ($level != "all") {
            $level = $this->getPointsByName($level);
        }

        // Call the repository method to fetch creators based on the criteria
        return $this->em->getRepository(Organizer::class)->getCreators($level, $keyword, $selectedCategory, $limit, $sortedBy);
    }

    public function getPointsByName($name)
    {
        // Define a mapping of names to points
        $nameToPointsMapping = [
            "UniverseMaster" => 40,
            "UniverseRunnerUp" => 30,
            "UniverseTryhard" => 20,
            "UniverseWannabe" => 10,
            "UniverseNewbie" => 0,
        ];

        // Check if the provided name exists in the mapping
        return $nameToPointsMapping[$name] ?? "all";
    }

    public function getHelpCenterCategoriesFront($criterias)
    {
        $parent = array_key_exists('parent', $criterias) ? $criterias['parent'] : "all";
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $order = array_key_exists('order', $criterias) ? $criterias['order'] : "name";
        $sort = array_key_exists('sort', $criterias) ? $criterias['sort'] : "ASC";

        $order = "articlescount";

        $cats = $this->em->getRepository(HelpCenterCategory::class)->getHelpCenterCategories($parent, $hidden, $keyword, $slug, $limit, $order, $sort)->getQuery()->getResult();

        usort($cats, static function ($a, $b) {
            $categoriesOrder = ['Attendee', 'Creator', 'Account'];

            $indexA = array_search($a->getName(), $categoriesOrder, true);
            $indexB = array_search($b->getName(), $categoriesOrder, true);

            return $indexA - $indexB;
        });

        return $cats;
    }

    public function getAttendeesBookedOfEvent($slug)
    {
        return $this->em->getRepository("App\Entity\User")->getAttendeesBookedOfEvent($slug)->getQuery()->getResult();
    }

    public function getUsersShuffled($criterias)
    {
        $role = array_key_exists('role', $criterias) ? $criterias['role'] : "all";
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $organizername = array_key_exists('organizername', $criterias) ? $criterias['organizername'] : "all";
        $organizerslug = array_key_exists('organizerslug', $criterias) ? $criterias['organizerslug'] : "all";
        $username = array_key_exists('username', $criterias) ? $criterias['username'] : "all";
        $email = array_key_exists('email', $criterias) ? $criterias['email'] : "all";
        $firstname = array_key_exists('firstname', $criterias) ? $criterias['firstname'] : "all";
        $lastname = array_key_exists('lastname', $criterias) ? $criterias['lastname'] : "all";
        $enabled = array_key_exists('enabled', $criterias) ? $criterias['enabled'] : true;
        $followedby = array_key_exists('followedby', $criterias) ? $criterias['followedby'] : "all";
        $apiKey = array_key_exists('apikey', $criterias) ? $criterias['apikey'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $hasboughtticketforEvent = array_key_exists('hasboughtticketfor', $criterias) ? $criterias['hasboughtticketfor'] : "all";
        $hasboughtticketforOrganizer = array_key_exists('hasboughtticketfororganizer', $criterias) ? $criterias['hasboughtticketfororganizer'] : "all";
        $hasbookedticketfor = array_key_exists('hasbookedticketfor', $criterias) ? $criterias['hasbookedticketfor'] : "all";
        $isOnHomepageSlider = array_key_exists('isOnHomepageSlider', $criterias) ? $criterias['isOnHomepageSlider'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->em->getRepository("App\Entity\User")->getUsersShuffled($role, $keyword, $organizername, $organizerslug, $username, $email, $firstname, $lastname, $enabled, $slug, $followedby, $hasboughtticketforEvent, $hasboughtticketforOrganizer, $hasbookedticketfor, $apiKey, $isOnHomepageSlider, $limit, $count);
    }

    /**
     * @param $criterias
     * @return void
     * @throws Exception
     */
    public function getEventsHomepageIndex($criterias)
    {
        // Extract criteria variables
        $category = $criterias['category'] ?? "all";
        $venue = $criterias['venue'] ?? "all";
        $organizer = $criterias['organizer'] ?? "all";
        $open = $criterias['open'] ?? "all";
        $keyword = $criterias['keyword'] ?? "all";
        $onlineonly = $criterias['onlineonly'] ?? false;
        $startDateFilter = $criterias['startdate'] ?? null;
        $endDateFilter = $criterias['enddate'] ?? null;
        $slug = $criterias['slug'] ?? "all";
        $otherthan = $criterias['otherthan'] ?? "all";
        $published = $criterias['published'] ?? "all";
        $sortedBy = $criterias['sortedby'] ?? "all";
        $order = $criterias['order'] ?? "ASC";
        $limit = $criterias['limit'] ?? "all";
        $count = $criterias['count'] ?? false;
        $elapsed = $criterias['elapsed'] ?? "all";

        // Create the query builder
        $queryBuilder = $this->em->getRepository(Event::class)->createQueryBuilder('e');

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
            $queryBuilder->andWhere("organizer.slug = :organizer")
                ->setParameter("organizer", $organizer);
        }

        if ($published !== "all") {
            $queryBuilder->andWhere('e.published = :published')->setParameter('published', $published);
        }

        if ($open !== "all") {
            $queryBuilder->andWhere('e.open = :open')->setParameter('open', $open);
        }

        if ($category !== "all") {
            // Add a condition for the category
            $queryBuilder
                ->andWhere('e.category = :category')
                ->setParameter('category', $category);
        }

        if ($venue !== "all") {
            // Add a condition for the category
            $queryBuilder
                ->andWhere('e.venue = :venue')
                ->setParameter('venue', $venue);
        }

        if ($keyword !== "all") {
            // Add a condition for the keyword
            $keyword = "%" . $keyword . "%";
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('e.name', ':keyword'),
                        $queryBuilder->expr()->like('e.description', ':keyword'),
                        $queryBuilder->expr()->like('e.tags', ':keyword'),
                    )
                )
                ->setParameter('keyword', $keyword);
        }

        if ($otherthan !== "all") {
            $queryBuilder->andWhere("e.slug != :otherthan")->setParameter("otherthan", $otherthan);
        }

        $queryBuilder
            ->leftJoin('e.eventdates', 'eventdates');

        if ($startDateFilter && $endDateFilter) {
            $queryBuilder->andWhere("eventdates.startdate >= :startDateFilter")->setParameter("startDateFilter", $startDateFilter);
            $queryBuilder->andWhere("eventdates.startdate <= :endDateFilter")->setParameter("endDateFilter", $endDateFilter);
            $queryBuilder->andWhere("e.published = 1");
        }
        if ($elapsed !== "all") {
            if ($elapsed === true || $elapsed == "1") {
                $queryBuilder->andWhere("eventdates.enddate < CURRENT_TIMESTAMP()");
            } else if ($elapsed === false || $elapsed == "0") {
                $queryBuilder->andWhere("eventdates.enddate >= CURRENT_TIMESTAMP()");
            }
        }

        switch ($sortedBy) {
            case 'created_asc':
                $queryBuilder->orderBy("e.createdAt", 'ASC');
                break;
            case 'created_desc':
                $queryBuilder->orderBy("e.createdAt", 'DESC');
                break;
            case 'updated_asc':
                $queryBuilder->orderBy("e.updatedAt", 'ASC');
                break;
            case 'updated_desc':
                $queryBuilder->orderBy("e.updatedAt", 'DESC');
                break;
            case 'title_asc':
                $queryBuilder->orderBy("e.name", 'ASC');
                break;
            case 'title_desc':
                $queryBuilder->orderBy("e.name", 'DESC');
                break;
            default:
                // Default sorting by created date in descending order
                $queryBuilder->orderBy("eventdates.startdate", 'ASC');
                break;
        }

        // Execute the query
        return $queryBuilder;
    }

    /**
     * @param $criterias
     * @return QueryBuilder
     * @throws Exception
     */
    public function getEventsHomepageIndexMain($criterias)
    {
        // Extract criteria variables
        $startDateFilter = $criterias['startdate'] ?? "all";
        $elapsed = $criterias['elapsed'] ?? "all";
        $published = $criterias['published'] ?? "all";

        // Create the query builder
        $queryBuilder = $this->em->getRepository(Event::class)->createQueryBuilder('e');

        if ($published !== "all") {
            $queryBuilder->andWhere('e.published = :published')->setParameter('published', $published);
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
        }

        $queryBuilder
            ->leftJoin('e.eventdates', 'eventdates');


        if ($startDateMin && $startDateMax) {
            $queryBuilder
                ->leftJoin('e.eventdates', 'eventdates')
                ->andWhere('eventdates.startdate >= :startDateMin')
                ->andWhere('eventdates.startdate <= :startDateMax')
                ->setParameter('startDateMin', $startDateMin->format('Y-m-d H:i:s'))
                ->setParameter('startDateMax', $startDateMax->format('Y-m-d H:i:s'));
        }
        if ($elapsed !== "all") {
            if ($elapsed === true || $elapsed == "1") {
                $queryBuilder->andWhere("eventdates.startdate < CURRENT_TIMESTAMP()");
            } else if ($elapsed === false || $elapsed == "0") {
                $queryBuilder->andWhere("eventdates.startdate >= CURRENT_TIMESTAMP()");
            }
        }
        // Execute the query
        return $queryBuilder->orderBy('eventdates.startdate', 'ASC')->setMaxResults(8);
    }

    public function getUsersTotal()
    {
        return $this->em->getRepository("App\Entity\User")->getTotalUsersCount();
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

    public function getEventCountForDateRangeOpen(DateTimeInterface $startDate, DateTimeInterface $endDate): int
    {
        $query = $this->em->createQuery('
        SELECT COUNT(DISTINCT e.id)
        FROM App\Entity\Event e
        LEFT JOIN App\Entity\EventDate ed WITH e.id = ed.event
        WHERE ed.startdate >= :startDate AND ed.enddate <= :endDate and e.open = 1 and e.published = 1
    ');

        $query->setParameters([
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return (int)$query->getSingleScalarResult();
    }

    public function getEventCountForDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): int
    {
        $query = $this->em->createQuery('
        SELECT COUNT(DISTINCT e.id)
        FROM App\Entity\Event e
        LEFT JOIN App\Entity\EventDate ed WITH e.id = ed.event
        WHERE ed.startdate >= :startDate and ed.startdate <= :endDate and e.published = 1
    ');

        $query->setParameters([
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return (int)$query->getSingleScalarResult();
    }
//
//           public function getEventCountForDateRange($duration, DateTimeInterface $startDate, DateTimeInterface $endDate): int
//                {
//                    $query = $this->em->createQuery('
//                 SELECT COUNT(DISTINCT e.id)
//                        FROM App\Entity\Event e
//                        LEFT JOIN App\Entity\EventDate ed WITH e.id = ed.event
//                        WHERE ed.enddate <= :endDate
//                            AND ed.startdate <= :startDate
//                            AND e.published = 1
//                                       AND DATE_DIFF(ed.startdate, ed.enddate) <= :duration
//            ');
//
//                    $query->setParameters([
//                        'endDate' => $endDate,
//                        'startDate' => $startDate,
//                                'duration' => $duration,
//                    ]);
//
//                    return (int)$query->getSingleScalarResult();
//                }
//

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
    public function changeLinkLocale($link)
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

    public function findEventDatesAttendedByUser(User $user, Event $event)
    {
        $qb = $this->em->getRepository(EventDate::class)->createQueryBuilder('ed');

        $qb
            ->select('ed')
            ->join('ed.tickets', 'et')
            ->join('ed.event', 'e')
            ->join('et.orderelements', 'oe')
            ->join('oe.order', 'o')
            ->join('o.user', 'u')
            ->where('o.status = :status')
            ->andWhere('u = :user')
            ->andWhere('e = :event')
            ->setParameter('status', 2)
            ->setParameter('user', $user)
            ->setParameter('event', $event);

        return $qb->getQuery()->getResult();
    }

    public function updateCreatorBadge(User $user)
    {
        $organizer = $user->getOrganizer();
        $eventrepo = $this->em->getRepository(Event::class);
        $events = $eventrepo->findEventsCreatedByOrganizer($organizer);
        $totalpoints = 0;
        $index = 1;

        foreach ($events as $event) {
            // Replace this with your logic to get the total attendees for each event
            $totalAttendees = count($this->findAttendeesOfEvent($event));

            $multiplier = 0.5; // Default multiplier
            if ($totalAttendees > 5) {
                $multiplier = 1; // If total attendees > 5, set multiplier to 1
            }

            $totalpoints += $multiplier * $index;
            $index++;
        }

// Update points of the user with the calculated total points
        $userrepo = $this->em->getRepository(User::class);
        $userrepo->updateUserPoints($user, $totalpoints);
    }

    public function findAttendeesOfEvent(Event $event)
    {
        $qb = $this->em->getRepository(User::class)->createQueryBuilder('u');

        $qb
            ->select('u')
            ->join('u.orders', 'o')
            ->join('o.orderelements', 'oe')
            ->join('oe.eventticket', 't')
            ->join('t.eventdate', 'ed')
            ->join('ed.event', 'e')
            ->where('o.status = :status')
            ->andWhere('e = :event')
            ->setParameter('status', 2)
            ->setParameter('event', $event);

        return $qb->getQuery()->getResult();
    }

    public function findBookedAttendeesOfEvent(Event $event)
    {
        $qb = $this->em->getRepository(User::class)->createQueryBuilder('u');

        $qb
            ->select('u')
            ->join('u.orders', 'o')
            ->join('o.orderelements', 'oe')
            ->join('oe.eventticket', 't')
            ->join('t.eventdate', 'ed')
            ->join('ed.event', 'e')
            ->where('o.status >= :status')
            ->andWhere('e = :event')
            ->setParameter('status', 1)
            ->setParameter('event', $event);

        return $qb->getQuery()->getResult();
    }

    public function updateAttendeeBadge(User $user)
    {
        $eventrepo = $this->em->getRepository(Event::class);
        $eventscount = count($eventrepo->findTotalEventsAttendedByUser($user));

//            dump($eventrepo(findTotalEventsAttendedByUser($user)));
//            die();

        $userrepo = $this->em->getRepository(User::class);

        if ($eventscount >= 0 && $eventscount <= 5) {
            $totalpoints = 1; // No points for 0-5 events attended.
        } elseif ($eventscount >= 6 && $eventscount <= 11) {
            $totalpoints = 2; // Points for 6-11 events attended.
        } elseif ($eventscount >= 12 && $eventscount <= 20) {
            $totalpoints = 3; // Points for 12-20 events attended.
        } elseif ($eventscount >= 21 && $eventscount <= 30) {
            $totalpoints = 4; // Points for 21-30 events attended.
        } else {
            $totalpoints = 5; // Set a default value or specify your logic.
        }

        $userrepo->updateUserPoints($user, $totalpoints);
    }

    public function getEventDatesByEvent($event)
    {
        $qb = $this->em->getRepository(EventDate::class)->createQueryBuilder('ed');

        $qb
            ->select('ed')
            ->join('ed.event', 'e')
            ->where('e = :event')
            ->setParameter('event', $event);

        return $qb->getQuery()->getResult();
    }

    public function findUsersAttendeedByEventSlug($eventSlug)
    {
        $qb = $this->em->getRepository(User::class)->createQueryBuilder('u')
            ->select('u')
            ->innerJoin('u.orders', 'o')
            ->innerJoin('o.orderelements', 'oe')
            ->where('o.status = 2')
            ->innerJoin('oe.eventticket', 'et')
            ->innerJoin('et.eventdate', 'ed')
            ->innerJoin('ed.event', 'e')
            ->where('e.slug = :eventSlug')
            ->setParameter('eventSlug', $eventSlug);

        return $qb->getQuery()->getResult();
    }

    private function generateUniqueSlug($title)
    {
        // Generate a slug based on the title
        $baseSlug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));

        // Generate a random string (e.g., 6 characters) to append to the slug
        $randomString = bin2hex(random_bytes(10)); // Adjust the number of bytes for desired length

        // Combine the base slug and random string
        $uniqueSlug = $baseSlug . '-' . $randomString;

        // Check if the generated slug already exists in the database
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Event::class);

        // Keep incrementing the random string until you find a unique slug
        $counter = 1;
        while ($repository->findOneBy(['slug' => $uniqueSlug])) {
            $counter++;
            $uniqueSlug = $baseSlug . '-' . $randomString . '-' . $counter;
        }

        return $uniqueSlug;
    }
}
