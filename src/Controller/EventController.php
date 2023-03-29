<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }
    #[Route('/addevent', name: 'addevent')]
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
            return $this->redirectToRoute('app_event');
        }
        return $this->render('event/addEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
