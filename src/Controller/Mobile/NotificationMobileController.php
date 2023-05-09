<?php
namespace App\Controller\Mobile;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mobile/notification")
 */
class NotificationMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findAll();

        if ($notifications) {
            return new JsonResponse($notifications, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, UserRepository $userRepository, EventRepository $eventRepository): JsonResponse
    {
        $notification = new Notification();

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
            $event = $eventRepository->find((int)$request->get("event"));
        if (!$event) {
            return new JsonResponse("event with id " . (int)$request->get("event") . " does not exist", 203);
        }
        
        

        $notification->constructor(
            $user,
            $event,
            $request->get("message"),
            (int)$request->get("hasRead"),
            DateTime::createFromFormat("d-m-Y", $request->get("createdAt")),
            $request->get("status")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($notification);
        $entityManager->flush();

        return new JsonResponse($notification, 200);

        
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, NotificationRepository $notificationRepository, UserRepository $userRepository, EventRepository $eventRepository): Response
    {
        $notification = $notificationRepository->find((int)$request->get("id"));

        if (!$notification) {
            return new JsonResponse(null, 404);
        }

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
        return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
            $event = $eventRepository->find((int)$request->get("event"));
        if (!$event) {
        return new JsonResponse("event with id " . (int)$request->get("event") . " does not exist", 203);
        }
        
        

        $notification->constructor(
            $user,
            $event,
            $request->get("message"),
            (int)$request->get("hasRead"),
            DateTime::createFromFormat("d-m-Y", $request->get("createdAt")),
            $request->get("status")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($notification);
        $entityManager->flush();

        return new JsonResponse($notification, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, NotificationRepository $notificationRepository): JsonResponse
    {
        $notification = $notificationRepository->find((int)$request->get("id"));

        if (!$notification) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($notification);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
}
