<?php
namespace App\Controller\Mobile;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Repository\EventCategoryRepository;
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
 * @Route("/mobile/event")
 */
class EventMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        if ($events) {
            return new JsonResponse($events, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, UserRepository $userRepository, EventCategoryRepository $eventCategoryRepository): JsonResponse
    {
        $event = new Event();

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
            $eventCategory = $eventCategoryRepository->find((int)$request->get("eventCategory"));
        if (!$eventCategory) {
            return new JsonResponse("eventCategory with id " . (int)$request->get("eventCategory") . " does not exist", 203);
        }
        
        
            $file = $request->files->get("file");
        if ($file) {
            $imageFileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('uploads_directory'), $imageFileName);
            } catch (FileException $e) {
                dd($e);
            }
        } else {
            if ($request->get("image")) {
                $imageFileName = $request->get("image");
            } else {
                $imageFileName = "null";
            }
        }

        $event->constructor(
            $user,
            $eventCategory,
            $request->get("title"),
            $request->get("description"),
            DateTime::createFromFormat("d-m-Y", $request->get("date")),
            $request->get("location"),
            $imageFileName
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        return new JsonResponse($event, 200);

        
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, EventRepository $eventRepository, UserRepository $userRepository, EventCategoryRepository $eventCategoryRepository): Response
    {
        $event = $eventRepository->find((int)$request->get("id"));

        if (!$event) {
            return new JsonResponse(null, 404);
        }

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
        return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
            $eventCategory = $eventCategoryRepository->find((int)$request->get("eventCategory"));
        if (!$eventCategory) {
        return new JsonResponse("eventCategory with id " . (int)$request->get("eventCategory") . " does not exist", 203);
        }
        
        
        $file = $request->files->get("file");
        if ($file) {
                $imageFileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('uploads_directory'), $imageFileName);
            } catch (FileException $e) {
            dd($e);
            }
        } else {
            if ($request->get("image")) {
                $imageFileName = $request->get("image");
            } else {
                $imageFileName = "null";
            }
        }

        $event->constructor(
            $user,
            $eventCategory,
            $request->get("title"),
            $request->get("description"),
            DateTime::createFromFormat("d-m-Y", $request->get("date")),
            $request->get("location"),
            $imageFileName
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        return new JsonResponse($event, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository): JsonResponse
    {
        $event = $eventRepository->find((int)$request->get("id"));

        if (!$event) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($event);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
    /**
     * @Route("/image/{image}", methods={"GET"})
     */
    public function getPicture(Request $request): BinaryFileResponse
    {
        return new BinaryFileResponse(
            $this->getParameter('uploads_directory') . "/" . $request->get("image")
        );
    }
    
}
