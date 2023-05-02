<?php

namespace App\Controller;

use App\Entity\EventCategory;
use App\Form\EventCategoryType;
use App\Repository\EventCategoryRepository;
use App\Repository\EventRepository;
use SearchEventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventCategoryController extends AbstractController
{
    #[Route('/event/category', name: 'app_event_category')]
    public function index(Request $request, EventCategoryRepository $eventCategoryRepository): Response
    {
        $searchTerm = $request->query->get('search');

        if ($searchTerm) {
            $eventCategories = $eventCategoryRepository->findBySearchTerm($searchTerm);
        } else {
            $eventCategories = $eventCategoryRepository->findAll();
        }

        return $this->render('event_category/index.html.twig', [
            'eventCategories' => $eventCategories,
        ]);
    }

    #[Route('/event/jkjkjcategory', name: 'app_vvvevent_category')]
    public function indexxx(EventCategoryRepository $eventCategoryRepository): Response
    {
        $eventCategories  = $eventCategoryRepository->findAll();
        return $this->render('event_category/index.html.twig', [
            'eventCategories' => $eventCategories,
        ]);
    }

    #[Route('/event/category/add', name: 'app_event_category_add')]
    public function add(Request $request): Response
    {
        $eventCategory = new EventCategory();

        $form = $this->createForm(EventCategoryType::class, $eventCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eventCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Event category created successfully');

            return $this->redirectToRoute('app_event_category');
        }

        return $this->render('event_category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event/category/{id}/delete', name: 'delete_eventCategory')]
    public function delete(EventCategory $eventCategory): Response
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($eventCategory);
        $em->flush();
        $this->addFlash('success', 'The event has been deleted successfully.');
        return $this->redirectToRoute('app_event_category');
    }

    #[Route('/event/category/{id}/edit', name: 'edit_eventCategory')]
    public function edit(Request $request, EventCategory $eventCategory): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $eventCategory = $entityManager->getRepository(EventCategory::class)->find($id);

        $form = $this->createForm(EventCategoryType::class, $eventCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('app_event_category');
        }
        return $this->render('event_category/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/event/category/{id}', name: 'event_category')]
    public function showEventsByCategory(EventCategory $eventCategories, EventCategoryRepository $eventCategoryRepository): Response
    {
        $events = $eventCategories->getEvents();
        $eventCategories  = $eventCategoryRepository->findAll();
        return $this->render('event_category/events_by_category.html.twig', [
            'events' => $events,
            'eventCategories' => $eventCategories,
        ]);
    }
}
