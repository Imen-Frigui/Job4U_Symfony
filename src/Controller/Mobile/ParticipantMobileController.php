<?php
namespace App\Controller\Mobile;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
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
 * @Route("/mobile/participant")
 */
class ParticipantMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        $participants = $participantRepository->findAll();

        if ($participants) {
            return new JsonResponse($participants, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, UserRepository $userRepository, EventRepository $eventRepository): JsonResponse
    {
        $participant = new Participant();

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
            $event = $eventRepository->find((int)$request->get("event"));
        if (!$event) {
            return new JsonResponse("event with id " . (int)$request->get("event") . " does not exist", 203);
        }
        
        

        $participant->constructor(
            $user,
            $event,
            $request->get("status")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($participant);
        $entityManager->flush();

        return new JsonResponse($participant, 200);

        
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, ParticipantRepository $participantRepository, UserRepository $userRepository, EventRepository $eventRepository): Response
    {
        $participant = $participantRepository->find((int)$request->get("id"));

        if (!$participant) {
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
        
        

        $participant->constructor(
            $user,
            $event,
            $request->get("status")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($participant);
        $entityManager->flush();

        return new JsonResponse($participant, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): JsonResponse
    {
        $participant = $participantRepository->find((int)$request->get("id"));

        if (!$participant) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($participant);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
}
