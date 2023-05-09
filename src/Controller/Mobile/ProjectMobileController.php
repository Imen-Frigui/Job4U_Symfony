<?php
namespace App\Controller\Mobile;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mobile/project")
 */
class ProjectMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        if ($projects) {
            return new JsonResponse($projects, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $project = new Project();

        
        

        $project->constructor(
            $request->get("nom"),
            $request->get("societe")
        );

        $em = $doctrine->getManager();
        $em->persist($project);
        $em->flush();

        return new JsonResponse($project, 200);

        
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, ProjectRepository $projectRepository, ManagerRegistry $doctrine): Response
    {
        $project = $projectRepository->find((int)$request->get("id"));

        if (!$project) {
            return new JsonResponse(null, 404);
        }

        
        

        $project->constructor(
            $request->get("nom"),
            $request->get("societe")
        );

        $em = $doctrine->getManager();
        $em->persist($project);
        $em->flush();

        return new JsonResponse($project, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): JsonResponse
    {
        $project = $projectRepository->find((int)$request->get("id"));

        if (!$project) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($project);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
}
