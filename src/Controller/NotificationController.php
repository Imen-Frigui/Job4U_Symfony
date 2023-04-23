<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications')]
    public function getNotifications(NotificationRepository $notificationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find(2);
        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasRead' => false,
        ]);

        return $this->render('list.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
