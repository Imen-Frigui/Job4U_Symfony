<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\User;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ParticipantController extends AbstractController
{
    #[Route('/participant', name: 'app_participant')]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route('/event/{eventId}/participant/add', name: 'participant_add')]
    public function Participate(Event $eventId): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Get the event and user entities based on their IDs
        $event = $em->getRepository(Event::class)->find($eventId);
        $user = $em->getRepository(User::class)->find(2);
        // Check if the user has already participated in the event
        $participant = $em->getRepository(Participant::class)->findOneBy(['event' => $eventId, 'user' => 2]);
        if ($participant) {
            // User has already participated in the event, display error message
            $message = 'You are already a participant in this event';
            $this->addFlash('warning', $message);
        } else { // User has not yet participated in the event, create a new participant entity and persist it to the database

            // Create a new participant entity and set the event and user properties
            $participant = new Participant();
            $participant->setEvent($event);
            $participant->setUser($user);

            // Persist the new participant to the database
            $em->persist($participant);
            $em->flush();

            $message = 'You have successfully joined the event';
            $this->addFlash('success', $message);
        }
        // Redirect back to the event show page
        return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
    }

    #[Route('/event/{eventId}/participant/{participantId}/remove', name: 'participant_delete')]
    public function deleteParticipant(Event $event, Participant $participant): Response
    {
        $currentUser = $this->getUser();
        $creatorId = $event->getCreator()->getId();

        // Check if the current user is the creator of the event or an admin
        if ($currentUser === $creatorId || $this->isGranted('ROLE_ADMIN')) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Participant removed successfully.');
        } else {
            $this->addFlash('error', 'You are not authorized to remove participants for this event.');
        }
        return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
    }

    #[Route('/event/{eventId}/participant/withdraw', name: 'participant_withdraw')]
    public function withdraw(Event $eventId): Response
    {
        $em = $this->getDoctrine()->getManager();

        $participant = $em->getRepository(Participant::class)->findOneBy(['user' => 2, 'event' => $eventId]);


        // Check if the current user is the participant of the event
        if ($participant->getUser()->getId() !== 2) {
            $this->addFlash('error', 'You are not authorized to withdraw from this event.');
            return $this->redirectToRoute('my_participations');
        }

        // Remove the participant from the database
        $em->remove($participant);
        $em->flush();

        // Redirect back to the event show page
        return $this->redirectToRoute('my_participations');
    }

    #[Route('/my-participations', name: 'my_participations')]
    public function MyParticipations(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find(2);

        $participations = $em->getRepository(Participant::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.event', 'e')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $this->render('participant/my_participations.html.twig', [
            'participations' => $participations,
        ]);
    }
    #[Route('/ban/participant/{participantId}', name: 'ban_participant')]
    public function banParticipant(int $participantId, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($participantId);

        if (!$participant) {
            throw $this->createNotFoundException('Participant not found.');
        }

        $participant->setStatus(Participant::STATUS_BANNED);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('event_participants', ['id' => $participant->getEvent()->getId()]);
    }
    #[Route('/accept/participant/{participantId}', name: 'accept_participant')]
    public function acceptParticipant(int $participantId, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($participantId);

        if (!$participant) {
            throw $this->createNotFoundException('Participant not found.');
        }

        $participant->setStatus(Participant::STATUS_ACCEPTED);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('event_participants', ['id' => $participant->getEvent()->getId()]);
    }


    #[Route('/Admin/participants/total', name: 'total_participants')]
    public function getTotalParticipants(ParticipantRepository $participantRepository): Response
    {
        $totalParticipants = $participantRepository->count([]);

        return $this->render('participant/dashboard.html.twig', [
            'totalParticipants' => $totalParticipants
        ]);
    }
}
