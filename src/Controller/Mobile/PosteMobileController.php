<?php
namespace App\Controller\Mobile;

use App\Entity\Poste;
use App\Repository\PosteRepository;
use App\Repository\UserRepository;
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
 * @Route("/mobile/poste")
 */
class PosteMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(PosteRepository $posteRepository): Response
    {
        $postes = $posteRepository->findAll();

        if ($postes) {
            return new JsonResponse($postes, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, UserRepository $userRepository): JsonResponse
    {
        $poste = new Poste();

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
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

        $poste->constructor(
            $user,
            $request->get("titre"),
            $request->get("description"),
            $imageFileName,
            $request->get("domaine"),
            DateTime::createFromFormat("d-m-Y", $request->get("date"))
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($poste);
        $entityManager->flush();

        return new JsonResponse($poste, 200);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, PosteRepository $posteRepository, UserRepository $userRepository): Response
    {
        $poste = $posteRepository->find((int)$request->get("id"));

        if (!$poste) {
            return new JsonResponse(null, 404);
        }

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
        return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
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

        $poste->constructor(
            $user,
            $request->get("titre"),
            $request->get("description"),
            $imageFileName,
            $request->get("domaine"),
            DateTime::createFromFormat("d-m-Y", $request->get("date"))
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($poste);
        $entityManager->flush();

        return new JsonResponse($poste, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, PosteRepository $posteRepository): JsonResponse
    {
        $poste = $posteRepository->find((int)$request->get("id"));

        if (!$poste) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($poste);
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
