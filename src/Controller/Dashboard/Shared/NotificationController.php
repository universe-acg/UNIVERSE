<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Shared;

use App\Service\NotificationService;
use App\Controller\BaseController as AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NotificationController extends AbstractController
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     *
     */
#[Route("/notifications", name: "notification_list")]
    public function listNotifications()
    {

        $user = $this->getUser();

        if ($user) {
            $notifications = $this->notificationService->getNotificationsByUserId($user->getId());

            return $this->render('Dashboard/Shared/Notifications/index.html.twig', [
                'notifications' => $notifications,
            ]);
        }

        return $this->redirectToRoute('fos_user_security_login'); // Redirect to the login page if the user is not authenticated.
    }

    /**
     *
     */
#[Route("/notifications/{id}", name: "notification_show")]
    public function showNotification($id)
    {

        $user = $this->getUser();

        if ($user) {
            $notification = $this->notificationService->getNotificationById($user->getId(), $id);

            if ($notification && $notification->getUserId() === $user->getId()) {
                return $this->render('Dashboard/Shared/Notifications/notification.html.twig', [
                    'notification' => $notification,
                ]);
            }

            return new JsonResponse(['error' => 'Notification not found or you do not have permission.'], 403);
        }

        return $this->redirectToRoute('fos_user_security_login'); // Redirect to the login page if the user is not authenticated.
    }

    /**
     *
     */
#[Route("/notifications/mark/all_as_read", name: "mark_all_notifications_as_read", methods: ["POST"])]
    public function markAllNotificationsAsRead(NotificationService $notificationService): Response
    {

        $user = $this->getUser();

        if ($user) {

            $notificationService->markAllNotificationsAsRead($user);

            $this->addFlash('success', 'All notifications have been marked as read.');

            return $this->redirectToRoute('notification_list');
        }

        return $this->redirectToRoute('fos_user_security_login'); // Redirect to the login page if the user is not authenticated.
    }

    /**
     *
     */
#[Route("/notifications/mark-as-read/{id}", name: "notification_mark_as_read_xhr", options: ["expose"=>true], methods: ["POST"])]
    public function markNotificationAsReadXhr($id)
    {

        $user = $this->getUser();

        if ($user) {
            $notification = $this->notificationService->getNotificationById($user->getId(), $id);

            if ($notification && $notification->getUserId() === $user->getId()) {
                $this->notificationService->markNotificationAsRead($user->getId(), $notification->getId());

                return new JsonResponse(['message' => 'Notification marked as read']);
            }
        }

        return new JsonResponse(['message' => 'Error marking notification as read'], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     *
     */
#[Route("/notifications/mark-as-read/{id}", name: "notification_mark_as_read", methods: ["POST"])]
    public function markNotificationAsRead($id)
    {

        $user = $this->getUser();

        if ($user) {
            $notification = $this->notificationService->getNotificationById($user->getId(), $id);

            if ($notification && $notification->getUserId() === $user->getId()) {
                $this->notificationService->markNotificationAsRead($user->getId(), $notification->getId());
            }

            $this->addFlash('success', 'Notification marked as read');

            return $this->redirectToRoute('notification_list');
        }

        return $this->redirectToRoute('fos_user_security_login'); // Redirect to the login page if the user is not authenticated.
    }
}
