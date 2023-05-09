<?php

namespace App\Controller\Mobile;

use App\Entity\Offre;
use App\Repository\OffreRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Repository\CategoriesRepository;
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
 * @Route("/mobile/offre")
 */
class OffreMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(OffreRepository $offreRepository): Response
    {
        $offres = $offreRepository->findAll();

        if ($offres) {
            return new JsonResponse($offres, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, ProjectRepository $projectRepository, UserRepository $userRepository, CategoriesRepository $categorieRepository, ManagerRegistry $doctrine): JsonResponse
    {
        $offre = new Offre();


        $project = $projectRepository->find((int)$request->get("project"));
        if (!$project) {
            return new JsonResponse("project with id " . (int)$request->get("project") . " does not exist", 203);
        }

        $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }

        $categorie = $categorieRepository->find((int)$request->get("categorie"));
        if (!$categorie) {
            return new JsonResponse("categorie with id " . (int)$request->get("categorie") . " does not exist", 203);
        }


        $offre->constructor(
            $project,
            $user,
            $categorie,
            $request->get("nom"),
            $request->get("description"),
            DateTime::createFromFormat("d-m-Y", $request->get("date")),
            $request->get("duree")
        );

        $em = $doctrine->getManager();
        $em->persist($offre);
        $em->flush();

        return new JsonResponse($offre, 200);


    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, OffreRepository $offreRepository, ProjectRepository $projectRepository, UserRepository $userRepository, CategoriesRepository $categorieRepository, ManagerRegistry $doctrine): Response
    {
        $offre = $offreRepository->find((int)$request->get("id"));

        if (!$offre) {
            return new JsonResponse(null, 404);
        }


        $project = $projectRepository->find((int)$request->get("project"));
        if (!$project) {
            return new JsonResponse("project with id " . (int)$request->get("project") . " does not exist", 203);
        }

        $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }

        $categorie = $categorieRepository->find((int)$request->get("categorie"));
        if (!$categorie) {
            return new JsonResponse("categorie with id " . (int)$request->get("categorie") . " does not exist", 203);
        }


        $offre->constructor(
            $project,
            $user,
            $categorie,
            $request->get("nom"),
            $request->get("description"),
            DateTime::createFromFormat("d-m-Y", $request->get("date")),
            $request->get("duree")
        );

        $em = $doctrine->getManager();
        $em->persist($offre);
        $em->flush();

        return new JsonResponse($offre, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, OffreRepository $offreRepository): JsonResponse
    {
        $offre = $offreRepository->find((int)$request->get("id"));

        if (!$offre) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($offre);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }


}
