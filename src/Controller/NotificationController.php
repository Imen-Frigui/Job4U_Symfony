<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\EventCategoryRepository;
use App\Repository\EventRepository;
use App\Repository\NotificationRepository;
use SearchEventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications')]
    public function getNotifications(NotificationRepository $notificationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        //$user = $em->getRepository(User::class)->find(2);
        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasRead' => false,
        ]);

        return $this->render('list.html.twig', [
            'notifications' => $notifications,
        ]);
    }
    #[Route('/notification/show/{eventId}/{notificationId}', name: 'notification_show')]
    public function markNotificationAsRead(Event $eventId, Notification $notificationId, EventCategoryRepository $eventCategoryRepository, EventRepository $eventRepository, NotificationRepository $notificationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        //$user = $em->getRepository(User::class)->find(2);
        $user = $this->getUser();

        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasRead' => false,
        ]);
        $events = $eventRepository->findAll();
        $form = $this->createForm(SearchEventType::class);

        $eventCategories  = $eventCategoryRepository->findAll();
        // Fetch the event and notification entities
        $event = $em->getRepository(Event::class)->find($eventId);
        $notification = $em->getRepository(Notification::class)->find($notificationId);

        // Check if the entities exist
        if (!$event || !$notification) {
            throw $this->createNotFoundException('The event or notification does not exist.');
        }

        // Set the notification status as read
        $notification->setHasRead(true);
        $em->flush();


        return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
    }
}
