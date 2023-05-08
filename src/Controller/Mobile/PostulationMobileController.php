<?php

namespace App\Controller\Mobile;

use App\Entity\Postulation;
use App\Repository\PostulationRepository;
use App\Repository\UsersRepository;
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
 * @Route("/mobile/postulation")
 */
class PostulationMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(PostulationRepository $postulationRepository): Response
    {
        $postulations = $postulationRepository->findAll();

        if ($postulations) {
            return new JsonResponse($postulations, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, UsersRepository $userRepository): JsonResponse
    {
        $postulation = new Postulation();


        $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }


        $postulation->constructor(
            $user,
            $request->get("adresse"),
            $request->get("email"),
            DateTime::createFromFormat("d-m-Y", $request->get("date"))
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($postulation);
        $entityManager->flush();

        return new JsonResponse($postulation, 200);


    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, PostulationRepository $postulationRepository, UsersRepository $userRepository): Response
    {
        $postulation = $postulationRepository->find((int)$request->get("id"));

        if (!$postulation) {
            return new JsonResponse(null, 404);
        }


        $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }


        $postulation->constructor(
            $user,
            $request->get("adresse"),
            $request->get("email"),
            DateTime::createFromFormat("d-m-Y", $request->get("date"))
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($postulation);
        $entityManager->flush();

        return new JsonResponse($postulation, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, PostulationRepository $postulationRepository): JsonResponse
    {
        $postulation = $postulationRepository->find((int)$request->get("id"));

        if (!$postulation) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($postulation);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }


}
