<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Entity\User;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/', name: 'app_event')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
    
        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/add', name: 'event_add')]
    public function addEvent(Request $request): Response
    {
        // Get the EntityManager
        $entityManager = $this->getDoctrine()->getManager();

        // Get the user entity with id=1
        $creator = $entityManager->getRepository(User::class)->find(1);

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form ->isSubmitted() && $form->isValid()){
            $event->setCreator($creator);
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);//add
            $em->flush();
            return $this->redirectToRoute('event_list');
        }
        return $this->render('event/addEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event/list', name: 'event_list')]
    public function list(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
    
        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/delete/{id}', name: 'event_delete')]
    public function delete(Event $event): Response
    {       
        // Check if the logged-in user is the creator of the event
        if ($event->getCreator() !== 1) {
            $this->addFlash('error', 'You are not authorized to edit this event.');
            return $this->redirectToRoute('app_event');
        }      
        $em = $this->getDoctrine()->getManager();
        if ($event->getCreator()->getId() === 1) {
            $em->remove($event);
            $em->flush();
            $this->addFlash('success', 'The event has been deleted successfully.');
        } else {
            $this->addFlash('error', 'You are not authorized to delete this event.');
        }
        return $this->redirectToRoute('app_event');
    }

    #[Route('/event/edit/{id}', name: 'event_edit')]
    public function edit(Request $request, Event $event): Response
    {
    // Check if the logged-in user is the creator of the event
    if ($event->getCreator() !== 1) {
        $this->addFlash('error', 'You are not authorized to edit this event.');
        return $this->redirectToRoute('app_event');
    }
    $id = $request->get('id');           
    $entityManager = $this->getDoctrine()->getManager();
    $event = $entityManager->getRepository(Event::class)->find($id);
    
    $form = $this->createForm(EventType::class,$event);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('app_event');
    }
    return $this->render('event/edit.html.twig',['form'=>$form->createView()]);

}

#[Route('/event/show/{id}', name: 'event_show')]

public function show(Event $event): Response
{
    return $this->render('event/show.html.twig', [
        'event' => $event,
    ]);
}

}