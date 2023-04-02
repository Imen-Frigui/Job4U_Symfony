<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

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

        // Create a new participant entity and set the event and user properties
        $participant = new Participant();
        $participant->setEvent($event);
        $participant->setUser($user);

        // Persist the new participant to the database
        $em->persist($participant);
        $em->flush();

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

    #[Route('/my-events', name: 'my_events')]
    public function myEvents(): Response
    {
        $participantId = 2;

        $entityManager = $this->getDoctrine()->getManager();
        
        $events = $entityManager->createQueryBuilder()
            ->select('e')
            ->from('App\Entity\Event', 'e')
            ->join('e.participants', 'p')
            ->where('p.user = :participantId')
            ->setParameter('participantId', $participantId)
            ->getQuery()
            ->getResult();
        

        return $this->render('participant/my_events.html.twig', [
            'events' => $events,
        ]);
    }


}
