<?php
namespace App\Controller\Mobile;

use App\Entity\EventCategory;
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
 * @Route("/mobile/eventCategory")
 */
class EventCategoryMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(EventCategoryRepository $eventCategoryRepository): Response
    {
        $eventCategorys = $eventCategoryRepository->findAll();

        if ($eventCategorys) {
            return new JsonResponse($eventCategorys, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $eventCategory = new EventCategory();

        
        

        $eventCategory->constructor(
            $request->get("description"),
            $request->get("name")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($eventCategory);
        $entityManager->flush();

        return new JsonResponse($eventCategory, 200);

        
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, EventCategoryRepository $eventCategoryRepository): Response
    {
        $eventCategory = $eventCategoryRepository->find((int)$request->get("id"));

        if (!$eventCategory) {
            return new JsonResponse(null, 404);
        }

        
        

        $eventCategory->constructor(
            $request->get("description"),
            $request->get("name")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($eventCategory);
        $entityManager->flush();

        return new JsonResponse($eventCategory, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, EventCategoryRepository $eventCategoryRepository): JsonResponse
    {
        $eventCategory = $eventCategoryRepository->find((int)$request->get("id"));

        if (!$eventCategory) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($eventCategory);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
}
