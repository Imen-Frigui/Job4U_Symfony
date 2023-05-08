<?php

namespace App\Controller\Mobile;

use App\Entity\Societe;
use App\Repository\SocieteRepository;
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
 * @Route("/mobile/societe")
 */
class SocieteMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(SocieteRepository $societeRepository): Response
    {
        $societes = $societeRepository->findAll();

        if ($societes) {
            return new JsonResponse($societes, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $societe = new Societe();


        $file = $request->files->get("file");
        if ($file) {
            $imageFileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('uploads_directory'), $imageFileName);
            } catch (FileException $e) {
                dd($e);
            }
        } else {
            if ($request->get("image")) {
                $imageFileName = $request->get("image");
            } else {
                $imageFileName = "null";
            }
        }

        $societe->constructor(
            $request->get("adresse"),
            $request->get("email"),
            $request->get("tel"),
            $request->get("domaine"),
            $imageFileName
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($societe);
        $entityManager->flush();

        return new JsonResponse($societe, 200);


    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, SocieteRepository $societeRepository): Response
    {
        $societe = $societeRepository->find((int)$request->get("id"));

        if (!$societe) {
            return new JsonResponse(null, 404);
        }


        $file = $request->files->get("file");
        if ($file) {
            $imageFileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('uploads_directory'), $imageFileName);
            } catch (FileException $e) {
                dd($e);
            }
        } else {
            if ($request->get("image")) {
                $imageFileName = $request->get("image");
            } else {
                $imageFileName = "null";
            }
        }

        $societe->constructor(
            $request->get("adresse"),
            $request->get("email"),
            $request->get("tel"),
            $request->get("domaine"),
            $imageFileName
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($societe);
        $entityManager->flush();

        return new JsonResponse($societe, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, SocieteRepository $societeRepository): JsonResponse
    {
        $societe = $societeRepository->find((int)$request->get("id"));

        if (!$societe) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($societe);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }


    /**
     * @Route("/image/{image}", methods={"GET"})
     */
    public function getPicture(Request $request): BinaryFileResponse
    {
        return new BinaryFileResponse(
            $this->getParameter('uploads_directory') . "/" . $request->get("image")
        );
    }

}
