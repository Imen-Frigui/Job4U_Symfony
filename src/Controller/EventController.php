<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\Thumbnail;
use App\Form\EventType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use App\Repository\EventCategoryRepository;
use App\Repository\EventRepository;
use App\Repository\NotificationRepository;
use App\Repository\ParticipantRepository;
use SearchEventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventController extends AbstractController
{
    #[Route('/', name: 'app_event')]
    public function index(EventRepository $eventRepository, EventCategoryRepository $eventCategoryRepository, PaginatorInterface $paginator, NotificationRepository $notificationRepository, Request $request): Response
    {
        $form = $this->createForm(SearchEventType::class);

        $em = $this->getDoctrine()->getManager();
        $user =$this->getUser();
        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasRead' => false,
        ]);
        $events = $paginator->paginate(
            $eventRepository->findAll(),
            $request->query->getInt('page', 1), // Get the page parameter or default to 1
            2 // Number of items per page
        );
        $eventCategories  = $eventCategoryRepository->findAll();
        return $this->render('event/list.html.twig', [
            'form' => $form->createView(),
            'notifications' => $notifications,
            'eventCategories' => $eventCategories,
            'events' => $events,
            'pageCount' => $events->getPageCount(),
            'route' => 'app_event'
        ]);
    }

    #[Route('/event/add', name: 'event_add')]
    public function addEvent(Request $request): Response
    {
        // Get the EntityManager
        $entityManager = $this->getDoctrine()->getManager();

        // Get the user entity with id=1
        $creator = $this->getUser();

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setCreator($creator);

            $file = $form->get('img')->getData();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );

            // ... other code to handle the $post object

            $event->setImg($fileName);

            $em = $this->getDoctrine()->getManager();
            $category = $entityManager->getRepository(EventCategory::class)->find($form->get('eventCategory')->getData());
            $event->setEventCategory($category);
            $em->persist($event); //add
            $em->flush();

            return $this->redirectToRoute('event_list');
        }
        return $this->render('event/addEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event/list', name: 'event_list')]
    public function list(EventRepository $eventRepository, EventCategoryRepository $eventCategoryRepository, NotificationRepository $notificationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(SearchEventType::class);

        $events = $paginator->paginate(
            $eventRepository->findAll(),
            $request->query->getInt('page', 1), // Get the page parameter or default to 1
            2 // Number of items per page
        );
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasRead' => false,
        ]);
        $eventCategories  = $eventCategoryRepository->findAll();

        return $this->render('event/list.html.twig', [
            'form' => $form->createView(),
            'notifications' => $notifications,
            'eventCategories' => $eventCategories,
            'events' => $events,
            'pageCount' => $events->getPageCount(),
            'route' => 'app_event'
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
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($id);

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('my_events');
        }
        return $this->render('event/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/event/show/{id}', name: 'event_show')]
    public function show(Event $event, EventCategoryRepository $eventCategoryRepository, EventRepository $eventRepository, NotificationRepository $notificationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasRead' => false,
        ]);
        $events = $eventRepository->findAll();
        $form = $this->createForm(SearchEventType::class);

        $eventCategories  = $eventCategoryRepository->findAll();
        return $this->render('event/show.html.twig', [
            'notifications' => $notifications,
            'events' => $events,
            'event' => $event,
            'eventCategories' => $eventCategories,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/my-events', name: 'my_events')]
    public function myEvents(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user =$this->getUser();


        //  $user = $em->getRepository(User::class)->find(1);
        $events = $em->getRepository(Event::class)->findBy(['creator' => $user]);
        return $this->render('event/my_events.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/{id}/participants', name: 'event_participants')]
    public function participants(Event $event): Response
    {
        // Get the list of participants for the event
        $participants = $event->getParticipants();

        // Render the participants list view
        return $this->render('event/participants.html.twig', [
            'event' => $event,
            'participants' => $participants,
        ]);
    }

    #[Route('/admin/events', name: 'list_events')]
    public function Adminlist(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('Admin/listEvent.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/admin/stats', name: 'eventstats')]
    public function eventStat(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Get all events
        $events = $entityManager->getRepository(Event::class)->findAll();

        // Get the selected event, if there is one
        $selectedEventId = $request->query->get('event');

        // Get the data for the selected event, if there is one
        $selectedEvent = null;
        $participantStatusCounts = [];
        if ($selectedEventId) {
            $selectedEvent = $entityManager->getRepository(Event::class)->find($selectedEventId);
            if ($selectedEvent) {
                // Count the number of participants for each status for the selected event
                $participants = $selectedEvent->getParticipants();
                $participantStatusCounts = [
                    'accepted' => 0,
                    'pending' => 0,
                    'banned' => 0,
                ];
                foreach ($participants as $participant) {
                    $status = $participant->getStatus();
                    if (isset($participantStatusCounts[$status])) {
                        $participantStatusCounts[$status]++;
                    }
                }
            }
        }

        return $this->render('Admin/listEvent.html.twig', [
            'events' => $events,
            'selectedEvent' => $selectedEvent,
            'participantStatusCounts' => $participantStatusCounts,
        ]);
    }

    #[Route('/admin/list/stats', name: 'event_list_stats')]
    public function eventList(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        $eventData = [];

        foreach ($events as $event) {
            $eventData[] = [
                'title' => $event->getTitle(),
                'accepted' => $eventRepository->getParticipantStatusCount($event->getId(), 'accepted'),
                'pending' => $eventRepository->getParticipantStatusCount($event->getId(), 'pending'),
                'banned' => $eventRepository->getParticipantStatusCount($event->getId(), 'banned')
            ];
        }

        return $this->render('Admin/ListE.html.twig', [
            'eventData' => json_encode($eventData)
        ]);
    }

    #[Route('/category/{id}/events/count', name: 'count_category_events')]
    public function countCategoryEvents(EventCategory $eventCategory): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $events = $entityManager->getRepository(Event::class)->findBy(['eventCategory' => $eventCategory]);
        $count = count($events);

        return $this->render('event/show.html.twig', [
            'category' => $eventCategory,
            'count' => $count,
        ]);
    }

    #[Route('/events/search', name: 'search_events')]
    public function search(Request $request): Response
    {
        $form = $this->createForm(SearchEventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $title = $data['title'];
            $category = $data['category'];

            if ($title !== null) {
                $events = $this->getDoctrine()->getRepository(Event::class)->findByTitleAndCategory($title, $category);
            } elseif ($category !== null) {
                $entityManager = $this->getDoctrine()->getManager();
                $events = $entityManager->getRepository(Event::class)->findBy(['eventCategory' => $category]);
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $events = $entityManager->getRepository(Event::class)->findAll();
            }

            return $this->render('event/search_results.html.twig', [
                'events' => $events,
                'form' => $form->createView(),
            ]);
        }
        // Determine the template to render based on the request origin
        $referer = $request->headers->get('referer');
        $template = 'event/list.html.twig';
        if (strpos($referer, '/events/') !== false) {
            $template = 'event/show.html.twig';
        }

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }
}
